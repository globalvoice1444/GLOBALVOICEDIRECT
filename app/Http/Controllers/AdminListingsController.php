<?php

namespace App\Http\Controllers;

use App\Support\BusinessListings as BL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Admin-only (adminauth group): Business Listings list/detail/status, Bing CSV export + download. */
class AdminListingsController extends Controller
{
    public function index(Request $r)
    {
        $st = in_array($r->query('status'), BL::STATUSES, true) ? $r->query('status') : '';
        $q = DB::table(BL::TABLE . ' as l')->leftJoin('wpgc_users as u', 'u.ID', '=', 'l.user_id')->select('l.*', 'u.display_name');
        if ($st) $q->where('l.status', $st);
        return view('admin.gvd-listings', [
            'rows' => $q->orderByDesc('l.updated_at')->paginate(50)->withQueryString(), 'st' => $st, 'row' => null,
            'queued' => DB::table(BL::TABLE)->where('status', 'bing_queued')->orderBy('id')->get(['id', 'phone', 'business_name']),
            'newCount' => DB::table(BL::TABLE)->where('status', 'new')->count(), 'files' => BL::exportFiles(),
            'next' => self::nextRun(),
        ]);
    }

    public static function nextRun(): string
    {
        $n = now('America/New_York')->setTime(2, 0); if ($n->isPast()) $n->addDay();
        return $n->format('M j, Y g:i A T');
    }

    public function show(int $id)
    {
        $row = DB::table(BL::TABLE . ' as l')->leftJoin('wpgc_users as u', 'u.ID', '=', 'l.user_id')->select('l.*', 'u.display_name')->where('l.id', $id)->first();
        if (!$row) abort(404);
        return view('admin.gvd-listings', ['rows' => null, 'st' => '', 'row' => $row]);
    }

    public function status(Request $r)
    {
        $s = (string) $r->input('status'); if (!in_array($s, BL::STATUSES, true)) abort(422);
        $ids = array_filter(array_map('intval', (array) ($r->input('ids') ?? [$r->input('id')])));
        if ($ids) DB::table(BL::TABLE)->whereIn('id', $ids)->update(['status' => $s, 'updated_at' => now()]);
        $back = $r->input('back') ? '/admin/forms/listings' : '/admin/forms/listings/' . reset($ids);
        return redirect($back)->with('message', count($ids) . ' listing(s) set to ' . $s . '.');
    }

    public function export()
    {
        $res = BL::buildExport(true);
        return redirect('/admin/forms/listings')->with('message', $res ? 'Export created: ' . $res[0] . ' (' . count($res[1]) . ' rows).' : 'Nothing to export.');
    }

    public function file(string $name)
    {
        if (!preg_match('/^bing-bulk-[\w-]+\.csv$/', $name)) abort(404);
        $p = BL::exportDir() . '/' . $name; if (!is_file($p)) abort(404);
        return response()->download($p, $name, ['Content-Type' => 'text/csv; charset=utf-8', 'Cache-Control' => 'private, no-store']);
    }

    public function guide(int $id)
    {
        $r = DB::table(BL::TABLE)->where('id', $id)->first(); if (!$r) abort(404);
        return response()->view('users.gvd-listing-guide', ['r' => $r])->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
