<?php

namespace App\Http\Controllers;

use App\Support\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/** Admin > Automations > Broadcasts (route group: adminauth). 2026-11-02 */
class AdminBroadcastController extends Controller
{
    private function back($id, $msg) { return redirect('/admin/automations/broadcasts' . ($id ? '?id=' . $id : ''))->with('message', $msg); }
    private function get($id) { $b = DB::table('broadcasts')->where('id', $id)->first(); abort_unless($b, 404); return $b; }

    public function index(Request $r)
    {
        $list = DB::table('broadcasts')->orderByDesc('id')->get();
        $counts = DB::table('broadcast_recipients')->selectRaw("broadcast_id, COUNT(*) total, SUM(status='pending') pending, SUM(status='sent') sent, SUM(status='failed') failed, SUM(status='skipped') skipped")
            ->groupBy('broadcast_id')->get()->keyBy('broadcast_id');
        $sel = $r->query('id') ? $list->firstWhere('id', (int) $r->query('id')) : null;
        $log = $sel ? DB::table('broadcast_recipients')->where('broadcast_id', $sel->id)->whereIn('status', $r->query('st') ? [$r->query('st')] : ['sent', 'failed', 'skipped', 'sending', 'pending'])
            ->orderByRaw("FIELD(status,'sending','failed','sent','skipped','pending')")->orderByDesc('updated_at')->paginate(25)->withQueryString() : null;
        $sample = $sel ? DB::table('broadcast_recipients')->where('broadcast_id', $sel->id)->whereNotNull('business_name')->orderBy('id')->first() : null;
        return view('admin.broadcasts', ['list' => $list, 'counts' => $counts, 'sel' => $sel, 'log' => $log, 'sample' => $sample,
            'today' => $sel ? Broadcast::sentToday($sel->id) : 0, 'enabled' => (bool) config('drip.enabled'), 'adminEmail' => session('admin_email'),
            'suppressed' => DB::table('email_suppressions')->count(), 'tz' => Broadcast::TZ]);
    }

    public function create(Request $r)
    {
        $d = $r->validate(['name' => 'required|max:150']);
        $id = DB::table('broadcasts')->insertGetId(['name' => $d['name'], 'subject' => $d['name'], 'body_html' => '<p>Hi {{first_name}},</p>', 'status' => 'DRAFT',
            'created_by' => session('admin_name'), 'created_at' => now(), 'updated_at' => now()]);
        return $this->back($id, 'Broadcast created as DRAFT.');
    }

    public function save(Request $r, $id)
    {
        $b = $this->get($id);
        $d = $r->validate(['name' => 'required|max:150', 'subject' => 'required|max:255', 'preview' => 'nullable|max:255', 'body_html' => 'required',
            'first_name_fallback' => 'required|max:60', 'business_fallback' => 'required|max:100', 'daily_cap' => 'required|integer|min:1|max:2000', 'start_at' => 'nullable|date']);
        $d['start_at'] = !empty($d['start_at']) ? \Carbon\Carbon::parse($d['start_at'], Broadcast::TZ)->utc() : null;
        DB::table('broadcasts')->where('id', $id)->update($d + ['updated_at' => now()]);
        return $this->back($id, 'Saved.');
    }

    public function audience(Request $r, $id)
    {
        $b = $this->get($id);
        if (!in_array($b->status, ['DRAFT', 'PAUSED'])) return $this->back($id, 'Pause the broadcast before changing its audience.');
        $mode = $r->input('mode');
        if ($mode === 'clear') { DB::table('broadcast_recipients')->where('broadcast_id', $id)->where('status', 'pending')->delete(); return $this->back($id, 'Pending recipients removed.'); }
        if ($mode === 'customers') {
            $n = Broadcast::addRecipients($id, Broadcast::customerAudience());
            DB::table('broadcasts')->where('id', $id)->update(['audience' => 'customers', 'updated_at' => now()]);
            Log::info('broadcast audience', ['broadcast' => (int) $id, 'mode' => 'customers', 'added' => $n, 'admin' => session('admin_name')]);
            return $this->back($id, "{$n} existing customer(s) added (verified, valid email, not demo, not unsubscribed; duplicates skipped).");
        }
        $r->validate(['csv' => 'required|file|max:8192|mimes:csv,txt']);
        [$rows, $st] = Broadcast::parseCsv(file_get_contents($r->file('csv')->getRealPath()));
        if (!empty($st['error'])) return $this->back($id, $st['error']);
        $n = Broadcast::addRecipients($id, $rows);
        DB::table('broadcasts')->where('id', $id)->update(['audience' => 'csv', 'updated_at' => now()]);
        Log::info('broadcast audience', ['broadcast' => (int) $id, 'mode' => 'csv', 'added' => $n, 'admin' => session('admin_name')]);
        return $this->back($id, "CSV: {$st['rows']} rows, {$n} added, " . (count($rows) - $n) . " already in list, {$st['dupes']} duplicates, {$st['invalid']} invalid, {$st['suppressed']} unsubscribed.");
    }

    public function status(Request $r, $id)
    {
        $b = $this->get($id); $to = $r->input('status');
        $ok = ['SCHEDULED' => ['DRAFT', 'PAUSED'], 'PAUSED' => ['SCHEDULED', 'SENDING'], 'SENDING' => ['PAUSED'], 'DRAFT' => ['PAUSED', 'SCHEDULED']];
        if (!isset($ok[$to]) || !in_array($b->status, $ok[$to])) return $this->back($id, "Can't move from {$b->status} to {$to}.");
        if ($to === 'SCHEDULED' || $to === 'SENDING') {
            if ($r->input('confirm') !== 'SEND') return $this->back($id, 'Type SEND to confirm. This emails real customers.');
            if (!DB::table('broadcast_recipients')->where('broadcast_id', $id)->where('status', 'pending')->exists()) return $this->back($id, 'No pending recipients.');
        }
        $upd = ['status' => $to, 'updated_at' => now()];
        if ($to === 'SCHEDULED' && !$b->start_at) $upd['start_at'] = now();
        DB::table('broadcasts')->where('id', $id)->update($upd);
        DB::table('broadcast_recipients')->where('broadcast_id', $id)->where('status', 'sending')->where('updated_at', '<', now()->subMinutes(30))->update(['status' => 'pending']);
        Log::info('broadcast status', ['broadcast' => (int) $id, 'from' => $b->status, 'to' => $to, 'admin' => session('admin_name')]);
        return $this->back($id, "Broadcast {$to}." . ($to === 'SCHEDULED' && !config('drip.enabled') ? ' Global sending is OFF, so nothing will go out until DRIP_SENDING=true.' : ''));
    }

    private function sample($b, $recId = null)
    {
        $q = DB::table('broadcast_recipients')->where('broadcast_id', $b->id);
        $r = $recId ? (clone $q)->where('id', $recId)->first() : (clone $q)->whereNotNull('business_name')->orderBy('id')->first();
        return $r ?: (object) ['email' => session('admin_email') ?: 'you@example.com', 'first_name' => 'Alex', 'business_name' => 'Alex\'s Plumbing LLC', 'extra' => '', 'user_id' => null];
    }

    public function preview(Request $r, $id)
    {
        $b = $this->get($id);
        $s = $r->query('demo') ? $this->sample((object) ['id' => 0]) : $this->sample($b, (int) $r->query('rec') ?: null);
        [$subj, $html] = Broadcast::render($b, $s);
        $html = str_replace('<body ', '<body data-subject="' . e($subj) . '" ', $html);
        return response($html)->header('Content-Security-Policy', "script-src 'none'");
    }

    /** ONE test to the signed-in admin's own address, rendered with the sample recipient's merge values. */
    public function test($id)
    {
        $b = $this->get($id);
        $to = strtolower(trim((string) session('admin_email')));
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return $this->back($id, 'Your admin account has no email address.');
        try {
            $s = $this->sample($b); $s = (object) ((array) $s); $s->email = $to;
            [$subj, $html] = Broadcast::render($b, $s);
            \App\Support\Drip::deliver($to, '[TEST] ' . $subj, $html, null);
            Log::info('broadcast test sent', ['broadcast' => (int) $id, 'admin' => session('admin_name')]);
            return $this->back($id, "Test sent to {$to}.");
        } catch (\Throwable $e) {
            return $this->back($id, 'Test failed: ' . mb_substr($e->getMessage(), 0, 150));
        }
    }
}
