<?php

namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Portal\WebPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class PhoneController extends Controller
{
    /** GET /user/phone */
    public function index()
    {
        if (\App\Portal\Demo::on()) return response()->view('users.phone', ['enabled' => true, 'demo' => true])->header('Cache-Control', 'private, no-store'); // sample-data login: UI only, never registers or dials
        $enabled = WebPhone::enabledFor(FaxInbox::tenantsForUser(session()->get('user_id')));
        return response()->view('users.phone', ['enabled' => (bool) $enabled])->header('Cache-Control', 'private, no-store');
    }

    /** POST /user/phone/credentials: SIP logins for this tenant's own extensions. Never cached or logged. */
    public function credentials(Request $req)
    {
        if (\App\Portal\Demo::on()) return \App\Portal\Demo::readonly(); // sample-data login: no calls
        $nc = ['Cache-Control' => 'no-store, max-age=0', 'Pragma' => 'no-cache'];
        $uid = session()->get('user_id');
        $allowed = WebPhone::enabledFor(FaxInbox::tenantsForUser($uid));
        if (!$allowed) return response()->json(['error' => 'The browser phone is not enabled for your account.'], 403, $nc);
        $key = 'webphone:' . $uid;
        if (RateLimiter::tooManyAttempts($key, 20)) return response()->json(['error' => 'Too many attempts. Wait a minute and try again.'], 429, $nc);
        RateLimiter::hit($key, 60);
        try {
            $r = WebPhone::credentials($allowed);
        } catch (\Throwable $e) {
            Log::warning('gvd-phone credentials failed', ['user_id' => $uid, 'error' => get_class($e)]);
            return response()->json(['error' => 'Phone service is unavailable right now. Please try again.'], 502, $nc);
        }
        Log::info('gvd-phone credentials', ['user_id' => $uid, 'status' => $r['status'], ] + ($r['stats'] ?? []));
        return response()->json($r['body'], $r['status'], $nc);
    }
}
