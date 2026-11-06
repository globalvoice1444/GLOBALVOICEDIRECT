<?php
namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Portal\BillingSession;
use App\Portal\VoData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/** Customer home (VO style): greeting, real stats, shortcuts, recent activity. Read-only; no new data. */
class VoHomeController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache'];

    public function index()
    {
        $uid = session()->get('user_id');
        if (\App\Portal\Demo::on()) return view('users.vo-home', ['u' => DB::table('wpgc_users')->where('ID', $uid)->first(['display_name', 'user_email']), 'faxWeek' => 5, 'recentFax' => \App\Portal\Demo::recentFax()]); // sample-data login only
        $u = $uid ? DB::table('wpgc_users')->where('ID', $uid)->first(['display_name', 'user_email']) : null;
        $faxWeek = 0; $recentFax = collect();
        try {
            $t = FaxInbox::tenantsForUser($uid);
            if ($t) {
                $q = DB::table('gvd_faxes')->where('status', 'stored')->whereIn('i_customer', $t);
                $faxWeek = (clone $q)->where('received_at', '>=', now()->subDays(7))->count();
                $recentFax = (clone $q)->orderByDesc('received_at')->limit(5)->get(['caller', 'did', 'received_at', 'pages']);
            }
        } catch (\Throwable $e) {}
        return view('users.vo-home', ['u' => $u, 'faxWeek' => $faxWeek, 'recentFax' => $recentFax]);
    }

    /** GET /user/home/stats: counts from the billing API for the signed-in tenant (cached 5 min). Missing values are null. */
    public function stats()
    {
        if (\App\Portal\Demo::on()) return response()->json(\App\Portal\Demo::stats(), 200, self::NC); // sample-data login only
        $ten = FaxInbox::tenantsForUser(session()->get('user_id'));
        $ic = BillingSession::storedCustomer();
        if ($ic === null || !in_array($ic, $ten, true)) return response()->json(['ok' => false], 200, self::NC);
        $out = Cache::get("gvd-home:stats:$ic");
        if (is_array($out)) return response()->json($out, 200, self::NC);
        $k = BillingSession::get();
        if (!$k || !in_array((int) $k['i_customer'], $ten, true)) return response()->json(['ok' => false, 'signin' => true], 200, self::NC);
        $v = new VoData($k);
        $out = ['ok' => true, 'calls_in' => null, 'calls_out' => null, 'extensions' => null, 'numbers' => null, 'recent' => []];
        $utc = new \DateTimeZone('UTC');
        $p = ['i_service' => 3, 'show_unsuccessful' => 1, 'from_date' => (new \DateTimeImmutable('-7 days', $utc))->format('Y-m-d H:i:s'), 'to_date' => (new \DateTimeImmutable('now', $utc))->format('Y-m-d H:i:s')];
        try { $r = $v->call('VO/get_customer_xdrs', $p + ['type' => 'inbound', 'limit' => 5, 'offset' => 0]); $out['calls_in'] = (int) ($r['total'] ?? count($r['xdr_list'] ?? []));
            $row = \Closure::bind(fn ($x) => self::row($x), null, VoPagesController::class);
            foreach (array_slice($r['xdr_list'] ?? [], 0, 5) as $x) { $y = $row($x); $out['recent'][] = ['time' => $y['time'], 'from' => $y['from'], 'status' => $y['status'], 'dur' => $y['dur']]; }
        } catch (\Throwable $e) {}
        try { $r = $v->call('VO/get_customer_xdrs', $p + ['type' => 'outbound', 'limit' => 1, 'offset' => 0]); $out['calls_out'] = (int) ($r['total'] ?? 0); } catch (\Throwable $e) {}
        try { $c = new VoPagesController(); $ext = \Closure::bind(fn ($v) => $this->extRows($v), $c, VoPagesController::class)($v);
            $out['extensions'] = count(array_filter($ext['rows'] ?? [], fn ($r) => ($r['type'] ?? '') === 'Account')); } catch (\Throwable $e) {}
        try { $c = new PhoneNumbersController(); $out['numbers'] = count(\Closure::bind(fn ($v) => $this->rows($v), $c, PhoneNumbersController::class)($v)); } catch (\Throwable $e) {}
        Cache::put("gvd-home:stats:$ic", $out, 300);
        return response()->json($out, 200, self::NC);
    }
}
