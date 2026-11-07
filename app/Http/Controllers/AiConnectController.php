<?php

namespace App\Http\Controllers;

use App\Portal\Demo;
use App\Support\AiConnect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Fax\FaxInbox;
use App\Portal\BillingSession;
use App\Portal\VoData;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/** 2026-11-02 Customer panel: AI Connect (API keys for AI assistants). Hidden unless AI_CONNECT_ENABLED, admin session, or demo login. */
class AiConnectController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0'];
    private function uid(): int { $u = (int) session()->get('user_id'); abort_unless($u > 0, 403); abort_unless(AiConnect::visibleTo($u), 404); return $u; }
    private function j($d, int $s = 200) { return response()->json($d, $s, self::NC); }

    public function index()
    {
        $uid = $this->uid();
        $keys = DB::table('ai_keys')->where('user_id', $uid)->where('prefix', '!=', \App\Support\AiOAuth::KEY_PREFIX)->orderByDesc('id')->get()->map(function ($k) {
            return ['id' => $k->id, 'name' => $k->name, 'prefix' => $k->prefix, 'scopes' => AiConnect::scopes($k), 'daily_limit' => (int) $k->daily_limit,
                'used_today' => AiConnect::usedToday($k->id), 'revoked' => (bool) $k->revoked_at,
                'created' => \Carbon\Carbon::parse($k->created_at)->tz('America/New_York')->format('M j, Y'),
                'last_used' => $k->last_used_at ? \Carbon\Carbon::parse($k->last_used_at)->tz('America/New_York')->format('M j, g:i A') . ' ET' : 'Never'];
        })->all();
        $names = DB::table('ai_keys')->where('user_id', $uid)->pluck('name', 'id');
        $perPage = 5;
        $activityQuery = DB::table('ai_activity')->where('user_id', $uid)->orderByDesc('id');
        $activityTotal = (clone $activityQuery)->count();
        $activityPages = $activityTotal === 0 ? 0 : (int) ceil($activityTotal / $perPage);
        $activityPage = max(1, (int) request()->query('activity_page', 1));
        if ($activityPages > 0 && $activityPage > $activityPages) $activityPage = $activityPages;
        $act = ($activityTotal === 0 ? collect() : (clone $activityQuery)->forPage($activityPage, $perPage)->get())->map(fn ($a) => [
            'when' => \Carbon\Carbon::parse($a->created_at)->tz('America/New_York')->format('M j, g:i A') . ' ET', 'key' => $names[$a->key_id] ?? '-',
            'what' => $a->tool ?: $a->method, 'mode' => $a->mode, 'status' => $a->status, 'summary' => $a->summary])->all();
        $d = ['keys' => $keys, 'activity' => $act, 'activity_page' => $activityPage, 'activity_pages' => $activityPages, 'enabled' => AiConnect::userEnabled($uid), 'scopes' => config('ai_connect.scopes'),
            'endpoint' => url('/mcp'), 'live' => (bool) config('ai_connect.enabled'), 'demo' => Demo::isDemoUser($uid), 'default_limit' => (int) config('ai_connect.default_daily_limit'),
            'max_limit' => (int) config('ai_connect.max_daily_limit'), 'calls' => (bool) config('ai_connect.calls_enabled')];
        // 2026-11-02 oauth: apps connected with "Sign in with Global Voice Direct"
        $d['oauth'] = \App\Support\AiOAuth::on();
        $d['apps'] = DB::table('ai_keys')->where('user_id', $uid)->where('prefix', \App\Support\AiOAuth::KEY_PREFIX)->whereNull('revoked_at')->orderByDesc('id')->get()->map(fn ($k) => [
            'id' => $k->id, 'name' => $k->name, 'scopes' => AiConnect::scopes($k), 'used_today' => AiConnect::usedToday($k->id), 'daily_limit' => (int) $k->daily_limit,
            'created' => \Carbon\Carbon::parse($k->created_at)->tz('America/New_York')->format('M j, Y'),
            'last_used' => $k->last_used_at ? \Carbon\Carbon::parse($k->last_used_at)->tz('America/New_York')->format('M j, g:i A') . ' ET' : 'Never'])->all();
        // 2026-11-02 mcp2: calling line
        $ln = AiConnect::line($uid);
        $d['line'] = $ln ? ['account' => AiConnect::fmt($ln->account), 'raw' => $ln->account, 'linked' => \Carbon\Carbon::parse($ln->linked_at)->tz('America/New_York')->format('M j, Y g:i A') . ' ET'] : null;
        $d['lines'] = []; $d['billing'] = false;
        if (Demo::isDemoUser($uid)) { $d['lines'] = [['id' => \App\Portal\Demo::MAIN, 'label' => AiConnect::fmt(\App\Portal\Demo::MAIN)]]; }
        elseif (!$ln && ($v = $this->vo())) {
            try { foreach ($v->accounts([], true) as $a) $d['lines'][] = ['id' => (string) $a['id'], 'label' => AiConnect::fmt((string) $a['id'])]; $d['billing'] = true; } catch (\Throwable $e) {}
            finally { try { $v->close(); } catch (\Throwable $e) {} }
        }
        return response()->view('users.gvd-ai-connect', ['d' => $d])->withHeaders(self::NC);
    }

    public function create(Request $r)
    {
        $uid = $this->uid();
        if (Demo::isDemoUser($uid)) return $this->j(['error' => Demo::READONLY], 422);
        if (DB::table('ai_keys')->where('user_id', $uid)->where('prefix', '!=', \App\Support\AiOAuth::KEY_PREFIX)->whereNull('revoked_at')->count() >= (int) config('ai_connect.max_keys')) return $this->j(['error' => 'You can have up to ' . config('ai_connect.max_keys') . ' active keys. Revoke one first.'], 422);
        $scopes = array_values(array_intersect(array_keys(config('ai_connect.scopes')), (array) $r->input('scopes', [])));
        if (!$scopes) return $this->j(['error' => 'Choose at least one permission.'], 422);
        [$id, $plain] = AiConnect::createKey($uid, (string) $r->input('name', ''), $scopes, (int) $r->input('daily_limit', config('ai_connect.default_daily_limit')));
        AiConnect::log($uid, $id, 'key', null, null, 'ok', 'key created', $r->ip());
        return $this->j(['ok' => true, 'key' => $plain]);
    }

    public function revoke(Request $r)
    {
        $uid = $this->uid();
        if (Demo::isDemoUser($uid)) return $this->j(['error' => Demo::READONLY], 422);
        $n = DB::table('ai_keys')->where('user_id', $uid)->where('id', (int) $r->input('id'))->where('prefix', '!=', \App\Support\AiOAuth::KEY_PREFIX)->whereNull('revoked_at')->update(['revoked_at' => now(), 'updated_at' => now()]);
        if ($n) AiConnect::log($uid, (int) $r->input('id'), 'key', null, null, 'ok', 'key revoked', $r->ip());
        return $this->j(['ok' => (bool) $n]);
    }

    /** 2026-11-02 oauth: disconnect an app (its grant key and every token). Allowed even when the flag is off, so access can always be cut. */
    public function revokeApp(Request $r)
    {
        $uid = $this->uid();
        $ok = \App\Support\AiOAuth::revokeGrant($uid, (int) $r->input('id'));
        if ($ok) AiConnect::log($uid, (int) $r->input('id'), 'oauth', null, null, 'ok', 'app disconnected', $r->ip());
        return $this->j(['ok' => $ok]);
    }

    public function toggle(Request $r)
    {
        $uid = $this->uid();
        if (Demo::isDemoUser($uid)) return $this->j(['error' => Demo::READONLY], 422);
        $on = filter_var($r->input('enabled'), FILTER_VALIDATE_BOOLEAN);
        AiConnect::setUserEnabled($uid, $on);
        AiConnect::log($uid, null, 'setting', null, null, 'ok', $on ? 'AI Connect turned on' : 'AI Connect turned off', $r->ip());
        return $this->j(['ok' => true, 'enabled' => $on]);
    }

    /** Signed-in customer's billing session, or null (same as Audio Campaign). */
    private function vo(): ?VoData
    {
        try {
            $k = BillingSession::get();
            if (!$k) return null;
            if (!in_array((int) $k['i_customer'], FaxInbox::tenantsForUser(session()->get('user_id')), true)) return null;
            return new VoData($k);
        } catch (\Throwable $e) { return null; }
    }

    /** Link a line for AI calling: checked exactly like an Audio Campaign launch; its call password is stored encrypted for this customer only. */
    public function linkLine(Request $r)
    {
        $uid = $this->uid();
        if (Demo::isDemoUser($uid)) return $this->j(['error' => Demo::READONLY], 422);
        $acct = preg_replace('/[^0-9A-Za-z_.@-]/', '', (string) $r->input('account', ''));
        if ($acct === '') return $this->j(['error' => 'Choose a line.'], 422);
        if (in_array(substr(preg_replace('/\D/', '', $acct), -10), AiConnect::NEVER_DIAL, true)) return $this->j(['error' => 'This line can\'t be used for AI calling.'], 422);
        $v = $this->vo();
        if (!$v) return $this->j(['error' => 'Your sign-in has expired. Please sign out and sign in again.'], 422);
        try {
            $a = $v->call('Account/get_account_info', ['id' => $acct, 'detailed_info' => 1])['account_info'] ?? null;
            if (!$a || (int) ($a['i_customer'] ?? 0) !== $v->ic) return $this->j(['error' => 'That line is not on your account.'], 403);
            if ($why = AiConnect::accountProblem($a)) return $this->j(['error' => $why], 422);
            $sip = (string) ($a['h323_password'] ?? ''); $web = (string) ($a['password'] ?? ''); // mcp6: keep both; the callback form uses the account's web password
            if ($sip === '' && $web === '') return $this->j(['error' => 'This line cannot place calls. Contact our support team.'], 422);
            $secret = json_encode(['web' => $web, 'sip' => $sip]);
        } catch (\Throwable $e) {
            Log::warning('ai-connect line check failed', ['uid' => $uid, 'error' => get_class($e)]);
            return $this->j(['error' => 'We could not check your line right now. Please try again.'], 422);
        } finally { try { $v->close(); } catch (\Throwable $e) {} }
        DB::table('ai_call_lines')->updateOrInsert(['user_id' => $uid], ['account' => $acct, 'secret_enc' => Crypt::encryptString($secret), 'linked_at' => now(), 'updated_at' => now(), 'created_at' => now()]);
        AiConnect::log($uid, null, 'line', null, null, 'ok', 'calling line linked: ' . AiConnect::fmt($acct) . ' (web key ' . (json_decode($secret, true)['web'] !== '' ? 'yes' : 'no') . ', service key ' . (json_decode($secret, true)['sip'] !== '' ? 'yes' : 'no') . ')', $r->ip());
        return $this->j(['ok' => true]);
    }

    public function unlinkLine(Request $r)
    {
        $uid = $this->uid();
        if (Demo::isDemoUser($uid)) return $this->j(['error' => Demo::READONLY], 422);
        $n = DB::table('ai_call_lines')->where('user_id', $uid)->delete();
        if ($n) AiConnect::log($uid, null, 'line', null, null, 'ok', 'calling line unlinked', $r->ip());
        return $this->j(['ok' => true]);
    }
}
