<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Admin-only alerts list (route group: adminauth). */
class AdminAlertsController extends Controller
{
    public function index(Request $r)
    {
        $type = (string) $r->query('type', ''); $state = (string) $r->query('state', '');
        $q = DB::table('gvd_admin_alerts as a')->leftJoin('wpgc_users as u', 'u.ID', '=', 'a.user_id')
            ->select('a.*', 'u.display_name', 'u.user_email');
        if ($type !== '') $q->where('a.type', $type);
        if ($state === 'unread') $q->whereNull('a.read_at');
        $alerts = $q->orderByDesc('a.id')->paginate(50)->withQueryString();
        $types = DB::table('gvd_admin_alerts')->select('type')->distinct()->orderBy('type')->pluck('type');
        return view('admin.alerts', compact('alerts', 'types', 'type', 'state'));
    }

    public function markRead(Request $r)
    {
        $q = DB::table('gvd_admin_alerts')->whereNull('read_at');
        if ($r->input('all')) { if ($r->input('type')) $q->where('type', (string) $r->input('type')); }
        else $q->whereIn('id', array_map('intval', (array) $r->input('ids', [])));
        $n = $q->update(['read_at' => now(), 'updated_at' => now()]);
        return redirect()->back()->with('message', $n . ' alert(s) marked as read.');
    }
}
