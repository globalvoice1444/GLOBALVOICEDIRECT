<?php

namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Portal\BillingSession;
use App\Portal\VoFax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/** Fax inbox and Send fax, live from the tenant's own Virtual Office mailboxes (signed-in billing session only). */
class VoFaxController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache'];
    const TYPES = ['pdf' => 'application/pdf', 'tif' => 'image/tiff', 'tiff' => 'image/tiff', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg'];

    private function svc(): VoFax|\Symfony\Component\HttpFoundation\Response
    {
        if (\App\Portal\Demo::on()) return \App\Portal\Demo::readonly(); // sample-data login: never touches the billing API
        $ten = FaxInbox::tenantsForUser(session()->get('user_id'));
        $ic = BillingSession::storedCustomer();
        if ($ic !== null && !in_array($ic, $ten, true)) return response()->json(['error' => 'Not allowed for this account.'], 403, self::NC);
        $k = BillingSession::get();
        if (!$k) return response()->json(['error' => 'Your sign-in has expired. Please sign out and sign in again.', 'signin' => true], 401, self::NC);
        if (!in_array((int) $k['i_customer'], $ten, true)) return response()->json(['error' => 'Not allowed for this account.'], 403, self::NC);
        return new VoFax($k);
    }

    private function err(?VoFax $v, \Throwable $e, string $what, string $msg = 'Could not load faxes right now. Please try again.')
    {
        Log::warning('gvd-fax failed', ['i_customer' => $v?->ic, 'what' => $what, 'error' => get_class($e), 'm' => substr(preg_replace('/[^\w .:]/', '', $e->getMessage()), 0, 80)]);
        return response()->json(['error' => $e instanceof \DomainException ? 'Not allowed.' : $msg], $e instanceof \DomainException ? 403 : 502, self::NC);
    }

    private static function et(string $d): string
    {
        try { return (new \DateTime($d))->setTimezone(new \DateTimeZone('America/New_York'))->format('M j, Y g:i A'); } catch (\Throwable $e) { return $d; }
    }
    private static function ph(string $n): string
    {
        $d = preg_replace('/\D/', '', $n);
        if (strlen($d) === 11 && $d[0] === '1') $d = substr($d, 1);
        return strlen($d) === 10 ? sprintf('(%s) %s-%s', substr($d, 0, 3), substr($d, 3, 3), substr($d, 6)) : $n;
    }

    /** GET /user/fax — page shell; live list loads from /user/fax/list. Saved copies (gvd_faxes) shown below. */
    public function inbox()
    {
        if (\App\Portal\Demo::on()) return response()->view('users.vo-fax', ['error' => null, 'admin' => true, 'faxes' => collect(), 'retentionDays' => (int) config('fax.retention_days', 90)])->withHeaders(self::NC);
        $v = $this->svc();
        $admin = $v instanceof VoFax ? $v->isAdmin() : false;
        $tenants = FaxInbox::tenantsForUser(session()->get('user_id'));
        $faxes = $tenants ? DB::table('gvd_faxes')->where('status', 'stored')->whereIn('i_customer', $tenants)->orderByDesc('received_at')->limit(500)->get() : collect();
        $faxes->each(function ($f) { $s = FaxInbox::sign((int) $f->i_customer, $f->uuid); $f->view_url = url("/user/fax/{$f->uuid}/file?exp={$s['exp']}&sig={$s['sig']}"); });
        $error = $v instanceof VoFax ? null : (json_decode($v->getContent(), true)['error'] ?? 'Not available');
        return response()->view('users.vo-fax', ['error' => $error, 'admin' => $admin, 'faxes' => $faxes, 'retentionDays' => (int) config('fax.retention_days', 90)])->withHeaders(self::NC);
    }

    /** GET /user/fax/list[?fresh=1] */
    public function list(Request $r)
    {
        if (\App\Portal\Demo::on()) return response()->json(['rows' => \App\Portal\Demo::faxInbox()], 200, self::NC);
        $v = $this->svc(); if (!$v instanceof VoFax) return $v;
        try {
            $in = $v->inbox($r->boolean('fresh'));
            if ($v->needsRescan() && ($k = BillingSession::get())) {
                // daily full rescan for new fax mailboxes, after the answer is sent
                app()->terminating(function () use ($k) { $w = new VoFax($k); try { $w->rescan(); } catch (\Throwable $e) {} finally { $w->close(); } });
            }
            $rows = array_map(fn ($x) => ['ia' => $x['ia'], 'uid' => $x['uid'], 'from' => self::ph($x['from']), 'to' => self::ph($x['to']), 'date' => self::et($x['date']),
                'ts' => strtotime($x['date']) ?: 0, 'seen' => $x['seen'], 'pages' => $x['pages']], $in['rows']);
            $rows = self::fillPages($rows);
            return response()->json(['rows' => $rows], 200, self::NC);
        } catch (\Throwable $e) { return $this->err($v, $e, 'list'); }
        finally { $v->close(); }
    }

    /** GET /user/fax/live/{ia}/{uid}[?download=1][&kind=out] — tenant-scoped proxy for the fax file. Does not mark it read. */
    public function file(Request $r, int $ia, string $uid)
    {
        if (\App\Portal\Demo::on()) return \App\Portal\Demo::faxFile((int) $uid, $r->boolean('download'));
        $v = $this->svc(); if (!$v instanceof VoFax) return $v;
        try {
            $kind = $r->query('kind') === 'out' ? 'out' : 'in';
            if (!$v->ownsFax($ia, $uid, $kind)) abort(404);
            $f = $v->file($ia, $uid);
            if (!$f) abort(404);
            $dl = $r->boolean('download');
            Log::info('gvd-fax file', ['i_customer' => $v->ic, 'user_id' => session()->get('user_id'), 'ia' => $ia, 'uid' => $uid, 'action' => $dl ? 'download' : 'view']);
            return response($f['body'], 200, ['Content-Type' => $f['type'], 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
                'Content-Disposition' => ($dl ? 'attachment' : 'inline') . '; filename="fax-' . preg_replace('/\D/', '', $uid) . '.' . $f['ext'] . '"']);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { throw $e; }
        catch (\Throwable $e) { return $this->err($v, $e, 'file', 'Could not open this fax.'); }
        finally { $v->close(); }
    }

    private function admin(): VoFax|\Symfony\Component\HttpFoundation\Response
    {
        $v = $this->svc(); if (!$v instanceof VoFax) return $v;
        if (!$v->isAdmin()) return response()->json(['error' => 'Only the company administrator can do this.'], 403, self::NC);
        $key = 'vo-write:' . session()->get('user_id');
        if (RateLimiter::tooManyAttempts($key, 30)) return response()->json(['error' => 'Too many changes. Wait a minute.'], 429, self::NC);
        RateLimiter::hit($key, 60);
        return $v;
    }

    /** POST /user/fax/delete {ia, uid, dry?} */
    public function delete(Request $r)
    {
        $v = $this->admin(); if (!$v instanceof VoFax) return $v;
        try {
            $ia = (int) $r->input('ia'); $uid = (string) $r->input('uid');
            if (!preg_match('/^\d+$/', $uid) || !$v->ownsFax($ia, $uid, 'in')) throw new \DomainException('not yours');
            if ($r->boolean('dry')) return response()->json(['ok' => true, 'dry' => true, 'method' => 'Voicemail/delete_mailbox_messages', 'as_account' => $ia, 'params' => ['message_uids' => [$uid]]], 200, self::NC);
            $v->delete($ia, $uid);
            Log::info('gvd-vo change', ['i_customer' => $v->ic, 'user_id' => session()->get('user_id'), 'what' => "fax delete $ia/$uid"]);
            return response()->json(['ok' => true], 200, self::NC);
        } catch (\Throwable $e) { return $this->err($v, $e, 'delete', 'The fax could not be deleted.'); }
        finally { $v->close(); }
    }

    /** GET /user/fax/send — Send fax page (history loads from /user/fax/sent). */
    public function sendPage()
    {
        if (\App\Portal\Demo::on()) return response()->view('users.vo-faxout', ['error' => null, 'admin' => true, 'hasFax' => true])->withHeaders(self::NC);
        $v = $this->svc();
        $error = $v instanceof VoFax ? null : (json_decode($v->getContent(), true)['error'] ?? 'Not available');
        $admin = $v instanceof VoFax ? $v->isAdmin() : false;
        $has = null;
        if ($v instanceof VoFax) { try { $has = $v->faxAccount() !== null; } catch (\Throwable $e) { $error = 'This page could not be loaded right now. Please try again.'; } finally { $v->close(); } }
        return response()->view('users.vo-faxout', ['error' => $error, 'admin' => $admin, 'hasFax' => $has])->withHeaders(self::NC);
    }

    /** GET /user/fax/sent[?fresh=1] */
    public function sent(Request $r)
    {
        if (\App\Portal\Demo::on()) return response()->json(['rows' => \App\Portal\Demo::faxSent(), 'ready' => true], 200, self::NC);
        $v = $this->svc(); if (!$v instanceof VoFax) return $v;
        try {
            $s = $v->sent($r->boolean('fresh'));
            $rows = array_map(fn ($x) => ['ia' => $x['ia'], 'uid' => $x['uid'], 'from' => self::ph($x['from']), 'to' => self::ph($x['to']), 'date' => self::et($x['date'] . (preg_match('/[a-z+]/i', $x['date']) ? '' : ' UTC')), 'status' => $x['status'], 'failed' => $x['failed'] ?? false, 'dur' => isset($x['secs']) ? sprintf('%d:%02d', intdiv($x['secs'], 60), $x['secs'] % 60) : '-'], $s['rows']);
            return response()->json(['rows' => $rows, 'ready' => $s['fax'] !== null], 200, self::NC);
        } catch (\Throwable $e) { return $this->err($v, $e, 'sent'); }
        finally { $v->close(); }
    }

    /** POST /user/fax/send {destination, file, save=1, dry?} — dry=1 returns the exact request without sending. */
    public function send(Request $r)
    {
        if (\App\Portal\Demo::on()) return response()->json(['ok' => true, 'dry' => true, 'message' => \App\Portal\Demo::READONLY], 200, self::NC); // sample-data login: never sends (UI shows 'Test only, nothing sent.')
        $v = $this->admin(); if (!$v instanceof VoFax) return $v;
        try {
            $dest = trim((string) $r->input('destination'));
            $d = preg_replace('/\D/', '', $dest);
            if (!preg_match('/^\+?[0-9() -]+$/', $dest) || strlen($d) < 10 || strlen($d) > 15) return response()->json(['error' => 'Enter a valid fax number.'], 422, self::NC);
            $f = $r->file('file');
            if (!$f || !$f->isValid()) return response()->json(['error' => 'Choose a file to send.'], 422, self::NC);
            $ext = strtolower($f->getClientOriginalExtension());
            if (!isset(self::TYPES[$ext])) return response()->json(['error' => 'File must be PDF, TIFF, JPG or JPEG.'], 422, self::NC);
            if ($f->getSize() > 20 * 1024 * 1024) return response()->json(['error' => 'File is too large (20 MB max).'], 422, self::NC);
            $real = (string) (new \finfo(FILEINFO_MIME_TYPE))->file($f->getRealPath());
            if (!in_array($real, ['application/pdf', 'image/tiff', 'image/jpeg'], true)) return response()->json(['error' => 'That file does not look like a PDF, TIFF or JPEG.'], 422, self::NC);
            $name = preg_replace('/[^\w.-]+/', '_', pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ext;
            $dry = $r->boolean('dry') || config('fax.send_dry_run', false);
            $res = $v->send($d, $f->getRealPath(), $name, self::TYPES[$ext], $r->boolean('save', true), $dry);
            Log::info('gvd-vo change', ['i_customer' => $v->ic, 'user_id' => session()->get('user_id'), 'what' => ($dry ? 'fax send DRY to ' : 'fax send to ') . substr($d, 0, -4) . 'xxxx']);
            return response()->json(['ok' => true] + $res, 200, self::NC);
        } catch (\Throwable $e) { return $this->err($v, $e, 'send', 'The fax could not be sent.'); }
        finally { $v->close(); }
    }

    /** Pages column: when the mailbox headers carry no page count, use the saved copy of the same fax (same sender + number, within 10 minutes). Read-only. */
    private static function fillPages(array $rows): array
    {
        if (!array_filter($rows, fn ($r) => $r['pages'] === null)) return $rows;
        try {
            $t = \App\Fax\FaxInbox::tenantsForUser(session()->get('user_id'));
            if (!$t) return $rows;
            $d10 = fn ($x) => substr(preg_replace('/\D/', '', (string) $x), -10);
            $saved = \Illuminate\Support\Facades\DB::table('gvd_faxes')->where('status', 'stored')->whereIn('i_customer', $t)->whereNotNull('pages')->get(['caller', 'did', 'received_at', 'pages'])
                ->map(fn ($f) => [$d10($f->caller), $d10($f->did), \Carbon\Carbon::parse($f->received_at, 'UTC')->timestamp, (int) $f->pages])->all();
            foreach ($rows as &$r) {
                if ($r['pages'] !== null) continue;
                foreach ($saved as $x) if ($x[0] === $d10($r['from']) && $x[1] === $d10($r['to']) && abs($x[2] - (int) $r['ts']) <= 600) { $r['pages'] = $x[3]; break; }
            }
            unset($r);
        } catch (\Throwable $e) {}
        return $rows;
    }
}
