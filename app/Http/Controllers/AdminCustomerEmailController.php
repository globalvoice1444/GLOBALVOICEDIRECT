<?php

namespace App\Http\Controllers;

use App\Support\CustomerEmail as CE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Admin > Automations > Customer email (route group: adminauth). Per-customer kill switch + limits. 2026-11-02 */
class AdminCustomerEmailController extends Controller
{
    public function index()
    {
        $since = now()->subDays(30);
        $rows = DB::table('ec_campaigns as c')->leftJoin('ec_recipients as r', 'r.campaign_id', '=', 'c.id')->leftJoin('wpgc_users as u', 'u.ID', '=', 'c.user_id')
            ->groupBy('c.user_id', 'u.user_login', 'u.user_email')
            ->selectRaw("c.user_id, u.user_login, u.user_email, COUNT(DISTINCT c.id) campaigns, SUM(r.status='sent' AND r.sent_at>=?) sent30, SUM(r.status='failed' AND r.updated_at>=?) failed30, SUM(r.reason='bounced') bounced, SUM(r.reason='complained') complained, SUM(c.status IN ('SCHEDULED','SENDING')) active", [$since, $since])
            ->orderByDesc('sent30')->get();
        $senders = DB::table('ec_senders')->get()->keyBy('user_id');
        $contacts = DB::table('ec_contacts')->groupBy('user_id')->selectRaw('user_id, COUNT(*) n')->pluck('n', 'user_id');
        return view('admin.customer-email', ['rows' => $rows, 'senders' => $senders, 'contacts' => $contacts, 'live' => (bool) config('ec.sending'),
            'def' => config('ec.daily_limit'), 'newLim' => config('ec.new_sender_limit')]);
    }

    public function update(Request $r)
    {
        $uid = (int) $r->input('user_id'); abort_unless($uid > 0, 422);
        DB::table('ec_senders')->insertOrIgnore(['user_id' => $uid, 'created_at' => now(), 'updated_at' => now()]);
        $blocked = $r->input('action') === 'block';
        $lim = $r->filled('daily_limit') ? max(0, min(5000, (int) $r->input('daily_limit'))) : null;
        $upd = ['updated_at' => now()];
        if (in_array($r->input('action'), ['block', 'unblock'], true)) $upd += ['blocked' => $blocked ? 1 : 0, 'blocked_reason' => $blocked ? (mb_substr(trim((string) $r->input('reason')), 0, 200) ?: 'Sending paused by Global Voice Direct support. Contact support@globalvoicedirect.com.') : null];
        if ($r->input('action') === 'limit') $upd['daily_limit'] = $lim ?: null;
        DB::table('ec_senders')->where('user_id', $uid)->update($upd);
        if ($blocked) DB::table('ec_campaigns')->where('user_id', $uid)->whereIn('status', ['SCHEDULED', 'SENDING'])->update(['status' => 'PAUSED', 'pause_reason' => $upd['blocked_reason'], 'updated_at' => now()]);
        return redirect('/admin/automations/customer-email')->with('message', 'Saved.');
    }
}
