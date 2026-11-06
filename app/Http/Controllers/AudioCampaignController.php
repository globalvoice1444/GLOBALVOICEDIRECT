<?php

namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Jobs\RunAudioCampaign;
use App\Portal\BillingSession;
use App\Portal\VoData;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/** Audio Campaign (2026-11-01): owner-scoped campaigns, recordings and per-number progress. Calls only when AUDIO_CAMPAIGN_SENDING=true. */
class AudioCampaignController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache'];
    const OFF = 'Calling not yet enabled';

    private function uid(): int { $u = (int) session()->get('user_id'); if (!$u) abort(403); return $u; }

    private function own(int $id): object
    {
        $c = DB::table('gvd_audio_campaigns')->where('id', $id)->first();
        if (!$c || (int) $c->user_id !== $this->uid()) abort(404);
        return $c;
    }

    /** Signed-in customer's billing session, or null (page still works for drafts). */
    private function vo(): ?VoData
    {
        try {
            $k = BillingSession::get();
            if (!$k) return null;
            if (!in_array((int) $k['i_customer'], FaxInbox::tenantsForUser(session()->get('user_id')), true)) return null;
            return new VoData($k);
        } catch (\Throwable $e) { return null; }
    }

    public function index(Request $req)
    {
        if (\App\Portal\Demo::on()) return response()->view('users.gvd-audio-campaigns', \App\Portal\Demo::audioIndex($req->query('c') ? (int) $req->query('c') : null))->withHeaders(self::NC); // sample-data login only
        $uid = $this->uid();
        $camps = DB::table('gvd_audio_campaigns')->where('user_id', $uid)->orderByDesc('id')->limit(100)->get();
        $counts = DB::table('gvd_audio_campaign_targets')->whereIn('campaign_id', $camps->pluck('id'))
            ->select('campaign_id', 'status', DB::raw('count(*) n'))->groupBy('campaign_id', 'status')->get()
            ->groupBy('campaign_id')->map(fn ($g) => $g->pluck('n', 'status')->all());
        $recs = DB::table('gvd_audio_recordings')->where('user_id', $uid)->orderByDesc('id')->get(['id', 'name', 'size', 'created_at']);
        $accounts = []; $billing = false;
        if ($v = $this->vo()) {
            try { foreach ($v->accounts([], true) as $a) $accounts[] = (string) $a['id']; $billing = true; } catch (\Throwable $e) {}
            finally { try { $v->close(); } catch (\Throwable $e) {} }
        }
        $show = $req->query('c') ? $this->own((int) $req->query('c')) : null;
        return response()->view('users.gvd-audio-campaigns', [
            'camps' => $camps, 'counts' => $counts, 'recs' => $recs, 'accounts' => $accounts, 'billing' => $billing,
            'show' => $show, 'sending' => (bool) config('audio_campaign.sending'), 'max' => (int) config('audio_campaign.max_numbers'), 'off' => self::OFF,
        ])->withHeaders(self::NC);
    }

    public function status(int $id)
    {
        if (\App\Portal\Demo::on()) { $d = \App\Portal\Demo::audioIndex($id); return response()->json(['status' => $d['show']->status, 'targets' => $d['demoTargets']], 200, self::NC); }
        $c = $this->own($id);
        $rows = DB::table('gvd_audio_campaign_targets')->where('campaign_id', $c->id)->orderBy('id')->get(['number', 'status', 'result', 'attempted_at']);
        return response()->json(['status' => $c->status, 'targets' => $rows], 200, self::NC);
    }

    /** Numbers from pasted text and/or the first column of a CSV. US 10/11 digits, de-duplicated. */
    private function numbers(Request $req): array
    {
        $lines = preg_split('/\r?\n|,|;/', (string) $req->input('numbers', ''));
        if ($req->hasFile('csv') && $req->file('csv')->isValid()) {
            $fh = fopen($req->file('csv')->getRealPath(), 'r'); $i = 0;
            while ($fh && ($row = fgetcsv($fh)) !== false && $i++ < 1000) $lines[] = (string) ($row[0] ?? '');
            if ($fh) fclose($fh);
        }
        $out = [];
        foreach ($lines as $l) {
            $d = preg_replace('/\D/', '', $l);
            if (strlen($d) === 10) $d = '1' . $d;
            if (strlen($d) === 11 && $d[0] === '1') $out[$d] = true;
        }
        return array_keys($out);
    }

    private function saveRecording(Request $req, int $uid): ?int
    {
        if (!$req->hasFile('recording')) return null;
        $f = $req->file('recording');
        $ext = strtolower($f->getClientOriginalExtension());
        if (!$f->isValid() || !in_array($ext, ['mp3', 'ogg', 'wav'], true)) throw new \InvalidArgumentException('The recording must be an MP3, OGG or WAV file.');
        $dir = "audio-campaigns/$uid"; $name = bin2hex(random_bytes(12)) . '.' . $ext;
        $stored = $f->storeAs($dir, $name, 'local');
        $src = \Illuminate\Support\Facades\Storage::disk('local')->path($stored);
        $au = preg_replace('/\.\w+$/', '.au', $stored);
        if (!\App\Audio\PromptAudio::convert($src, \Illuminate\Support\Facades\Storage::disk('local')->path($au))) {
            @unlink($src);
            throw new \InvalidArgumentException('We could not read that audio file. Please upload a different MP3, OGG or WAV file.');
        }
        return DB::table('gvd_audio_recordings')->insertGetId(['user_id' => $uid, 'au_path' => $au, 'play_token' => bin2hex(random_bytes(24)), 'name' => mb_substr(preg_replace('/[^\w .()-]/u', '_', $f->getClientOriginalName()), 0, 150),
            'path' => $stored, 'size' => (int) $f->getSize(), 'created_at' => now(), 'updated_at' => now()]);
    }

    public function store(Request $req)
    {
        if (\App\Portal\Demo::on()) return redirect('/user/audio-campaigns')->with('ac_err', \App\Portal\Demo::READONLY); // sample-data login: no calls
        $uid = $this->uid();
        $req->validate([
            'name' => 'required|string|max:150', 'numbers' => 'nullable|string|max:20000',
            'csv' => 'nullable|file|max:1024|mimes:csv,txt', 'recording' => 'nullable|file|max:' . (int) config('audio_campaign.max_upload_kb'),
            'recording_id' => 'nullable|integer', 'caller_account' => 'nullable|string|max:64',
            'when' => 'nullable|in:now,later', 'scheduled_at' => 'nullable|date', 'action' => 'required|in:draft,start',
        ]);
        $nums = $this->numbers($req);
        $max = (int) config('audio_campaign.max_numbers');
        if (!$nums) return back()->withInput()->with('ac_err', 'Add at least one valid US phone number.');
        if (count($nums) > $max) return back()->withInput()->with('ac_err', "A campaign can have up to $max numbers. You added " . count($nums) . '.');
        try { $recId = $this->saveRecording($req, $uid); } catch (\InvalidArgumentException $e) { return back()->withInput()->with('ac_err', $e->getMessage()); }
        $recId = $recId ?: ($req->filled('recording_id') ? (int) $req->input('recording_id') : null);
        if ($recId && !DB::table('gvd_audio_recordings')->where('id', $recId)->where('user_id', $uid)->exists()) abort(403);
        $when = $req->input('when') === 'later' && $req->filled('scheduled_at') ? Carbon::parse($req->input('scheduled_at'), 'America/New_York')->utc() : null;

        $id = DB::transaction(function () use ($uid, $req, $recId, $when, $nums) {
            $id = DB::table('gvd_audio_campaigns')->insertGetId(['user_id' => $uid, 'i_customer' => BillingSession::storedCustomer(),
                'name' => $req->input('name'), 'recording_id' => $recId, 'caller_account' => (string) $req->input('caller_account', ''),
                'status' => 'draft', 'scheduled_at' => $when, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('gvd_audio_campaign_targets')->insert(array_map(fn ($n) => ['campaign_id' => $id, 'number' => $n, 'status' => 'waiting', 'created_at' => now(), 'updated_at' => now()], $nums));
            return $id;
        });
        if ($req->input('action') === 'start') return $this->launch($id);
        return redirect('/user/audio-campaigns?c=' . $id)->with('ac_ok', 'Draft saved.');
    }

    public function start(int $id) { if (\App\Portal\Demo::on()) return redirect('/user/audio-campaigns')->with('ac_err', \App\Portal\Demo::READONLY); $this->own($id); return $this->launch($id); }

    private function launch(int $id)
    {
        $c = $this->own($id);
        $back = redirect('/user/audio-campaigns?c=' . $id);
        if (!in_array($c->status, ['draft', 'ready', 'paused'], true)) return $back->with('ac_err', 'This campaign has already started.');
        if (!$c->recording_id) return $back->with('ac_err', 'Choose a recording first.');
        if (!config('audio_campaign.sending') || (string) config('audio_campaign.ivr_number') === '') {
            DB::table('gvd_audio_campaigns')->where('id', $id)->update(['status' => 'ready', 'updated_at' => now()]);
            return $back->with('ac_err', self::OFF . '. Your campaign is saved and ready.');
        }
        if ($c->caller_account === '') return $back->with('ac_err', 'Choose the line to call from.');
        $v = $this->vo();
        if (!$v) return $back->with('ac_err', 'Your sign-in has expired. Please sign out and sign in again.');
        try {
            $a = $v->call('Account/get_account_info', ['id' => $c->caller_account, 'detailed_info' => 1])['account_info'] ?? null;
            if (!$a || (int) ($a['i_customer'] ?? 0) !== $v->ic) abort(403);
            $why = $this->accountProblem($a);
            if ($why) return $back->with('ac_err', $why);
            $secret = (string) ($a['h323_password'] ?? '');
            if ($secret === '') return $back->with('ac_err', 'This line cannot place campaign calls. Contact our support team.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { throw $e; }
        catch (\Throwable $e) {
            Log::warning('gvd-audio account check failed', ['campaign' => $id, 'error' => get_class($e)]);
            return $back->with('ac_err', 'We could not check your account right now. Please try again.');
        } finally { try { $v->close(); } catch (\Throwable $e) {} }

        DB::table('gvd_audio_campaigns')->where('id', $id)->update(['status' => 'queued', 'caller_secret' => Crypt::encryptString($secret), 'updated_at' => now()]);
        $job = (new RunAudioCampaign($id))->onConnection('database')->onQueue('audio-campaigns');
        if ($c->scheduled_at && Carbon::parse($c->scheduled_at, 'UTC')->isFuture()) $job->delay(Carbon::parse($c->scheduled_at, 'UTC'));
        dispatch($job);
        Log::info('gvd-audio campaign queued', ['campaign' => $id, 'user_id' => $c->user_id]);
        return $back->with('ac_ok', 'Campaign started.');
    }

    /** Same rules as the billing tool: blocked, over credit limit, expired. */
    private function accountProblem(array $a): ?string
    {
        if (($a['blocked'] ?? 'N') === 'Y') return 'This line is blocked. Contact our support team.';
        if ((int) ($a['billing_model'] ?? 0) === 1 && isset($a['credit_limit']) && $a['credit_limit'] !== null && (float) ($a['balance'] ?? 0) >= (float) $a['credit_limit'])
            return 'Your account has reached its credit limit.';
        if (!empty($a['expiration_date']) && date('Y-m-d') >= substr((string) $a['expiration_date'], 0, 10)) return 'This line has expired. Contact our support team.';
        if (($a['bill_status'] ?? 'O') !== 'O') return 'This line is not active.';
        return null;
    }

    public function pause(int $id)
    {
        if (\App\Portal\Demo::on()) return redirect('/user/audio-campaigns')->with('ac_err', \App\Portal\Demo::READONLY); // sample-data login: no calls
        $c = $this->own($id);
        if (in_array($c->status, ['queued', 'running'], true)) DB::table('gvd_audio_campaigns')->where('id', $id)->update(['status' => 'paused', 'updated_at' => now()]);
        return redirect('/user/audio-campaigns?c=' . $id)->with('ac_ok', 'Campaign paused.');
    }

    public function destroy(int $id)
    {
        if (\App\Portal\Demo::on()) return redirect('/user/audio-campaigns')->with('ac_err', \App\Portal\Demo::READONLY); // sample-data login: no calls
        $c = $this->own($id);
        if (in_array($c->status, ['queued', 'running'], true)) return redirect('/user/audio-campaigns?c=' . $id)->with('ac_err', 'Pause the campaign before deleting it.');
        DB::transaction(function () use ($id) {
            DB::table('gvd_audio_campaign_targets')->where('campaign_id', $id)->delete();
            DB::table('gvd_audio_campaigns')->where('id', $id)->delete();
        });
        return redirect('/user/audio-campaigns')->with('ac_ok', 'Campaign deleted.');
    }

    /** The converted recording for the billing platform to fetch (play_prompt). Unguessable per-recording token, no sign-in,
     *  served only while a campaign using it is ready, scheduled, running or paused. */
    public function playAudio(string $tok)
    {
        $tok = preg_replace('/\.au$/', '', $tok);
        $r = DB::table('gvd_audio_recordings')->where('play_token', $tok)->first();
        if (!$r || !$r->au_path) abort(404);
        if (!DB::table('gvd_audio_campaigns')->where('recording_id', $r->id)->whereIn('status', ['ready', 'queued', 'running', 'paused'])->exists()) abort(404);
        $base = realpath(\Illuminate\Support\Facades\Storage::disk('local')->path('audio-campaigns')); $full = realpath(\Illuminate\Support\Facades\Storage::disk('local')->path($r->au_path));
        if (!$base || !$full || !str_starts_with($full, $base . DIRECTORY_SEPARATOR) || !is_file($full)) abort(404);
        return response()->file($full, ['Content-Type' => 'audio/basic', 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'no-store', 'X-Robots-Tag' => 'noindex, nofollow']);
    }

    /** Owner-only playback of an uploaded recording (stored outside the public folder). */
    public function recording(int $id)
    {
        if (\App\Portal\Demo::on()) return response(\App\Portal\Demo::chimeWav(), 200, self::NC + ['Content-Type' => 'audio/wav']); // sample audio
        $r = DB::table('gvd_audio_recordings')->where('id', $id)->first();
        if (!$r || (int) $r->user_id !== $this->uid()) abort(404);
        $base = realpath(\Illuminate\Support\Facades\Storage::disk('local')->path('audio-campaigns')); $full = realpath(\Illuminate\Support\Facades\Storage::disk('local')->path($r->path));
        if (!$base || !$full || !str_starts_with($full, $base . DIRECTORY_SEPARATOR) || !is_file($full)) abort(404);
        $mime = str_ends_with($full, '.ogg') ? 'audio/ogg' : (str_ends_with($full, '.wav') ? 'audio/wav' : 'audio/mpeg');
        return response()->file($full, ['Content-Type' => $mime, 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']);
    }
}
