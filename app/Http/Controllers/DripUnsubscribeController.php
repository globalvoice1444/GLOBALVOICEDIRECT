<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Public, signed unsubscribe link for drip emails (2026-11-02). */
class DripUnsubscribeController extends Controller
{
    public function show(Request $r)
    {
        if (!$r->hasValidSignature()) abort(403, 'This unsubscribe link is invalid.');
        $email = strtolower((string) $r->query('e'));
        $done = DB::table('email_suppressions')->where('email', $email)->exists();
        return view('drip.unsubscribe', ['email' => $email, 'done' => $done, 'action' => $r->fullUrl()]);
    }

    public function store(Request $r)
    {
        if (!$r->hasValidSignature()) abort(403, 'This unsubscribe link is invalid.');
        $email = strtolower((string) $r->query('e'));
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            DB::table('email_suppressions')->insertOrIgnore(['email' => $email, 'reason' => 'unsubscribe', 'source' => 'drip-link', 'ip' => $r->ip(), 'created_at' => now(), 'updated_at' => now()]);
            DB::table('drip_enrollments')->where('email', $email)->where('status', 'active')->update(['status' => 'stopped', 'updated_at' => now()]);
        }
        if ($r->input('List-Unsubscribe') === 'One-Click') return response('', 200);
        return view('drip.unsubscribe', ['email' => $email, 'done' => true, 'action' => null]);
    }
}
