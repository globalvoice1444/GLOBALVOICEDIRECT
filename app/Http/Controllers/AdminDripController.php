<?php

namespace App\Http\Controllers;

use App\Support\Drip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/** Admin > Automations > Drip Sequences (route group: adminauth). 2026-11-02 */
class AdminDripController extends Controller
{
    private function back($seq, $msg) { return redirect('/admin/automations/drip?seq=' . $seq)->with('message', $msg); }

    public function index(Request $r)
    {
        $seqs = DB::table('drip_sequences')->orderByRaw("FIELD(type,'signup','motivation','winback','dunning','reengage')")->orderBy('id')->get();
        $sel = $seqs->firstWhere('id', (int) $r->query('seq')) ?: $seqs->first();
        $steps = $sel ? DB::table('drip_steps')->where('sequence_id', $sel->id)->orderBy('position')->get() : collect();
        $stat = fn($q) => $q->selectRaw("step_id, SUM(status='sent') sent, SUM(status='failed') failed, SUM(status='skipped') skipped")->groupBy('step_id')->get()->keyBy('step_id');
        $stepStats = $sel ? $stat(DB::table('drip_sends')->where('sequence_id', $sel->id)) : collect();
        $seqStats = DB::table('drip_sends')->selectRaw("sequence_id, SUM(status='sent') sent, SUM(status='failed') failed")->groupBy('sequence_id')->get()->keyBy('sequence_id');
        $enr = DB::table('drip_enrollments')->selectRaw("sequence_id, SUM(status='active') active, COUNT(*) total")->groupBy('sequence_id')->get()->keyBy('sequence_id');
        $recent = $sel ? DB::table('drip_sends')->where('sequence_id', $sel->id)->orderByDesc('id')->limit(15)->get() : collect();
        $suppressed = DB::table('email_suppressions')->count();
        return view('admin.drip', ['seqs' => $seqs, 'sel' => $sel, 'steps' => $steps, 'stepStats' => $stepStats, 'seqStats' => $seqStats, 'enr' => $enr,
            'recent' => $recent, 'suppressed' => $suppressed, 'enabled' => (bool) config('drip.enabled'), 'adminEmail' => session('admin_email'),
            'types' => Drip::TYPES, 'conds' => Drip::CONDITIONS]);
    }

    public function sequenceStatus(Request $r, $id)
    {
        $seq = DB::table('drip_sequences')->where('id', $id)->first(); abort_unless($seq, 404);
        $to = $r->input('status') === 'ACTIVE' ? 'ACTIVE' : 'PAUSED';
        $upd = ['status' => $to, 'updated_at' => now()];
        // never backfill old signups: auto-enrollment starts from the moment of activation
        if ($to === 'ACTIVE' && $seq->trigger === 'verified_signup' && (!$seq->enroll_after || $seq->enroll_after < now()->subDay())) $upd['enroll_after'] = now();
        DB::table('drip_sequences')->where('id', $id)->update($upd);
        // enrollments that have not received anything yet start their clock at activation (avoids a late burst / mass skip)
        if ($to === 'ACTIVE') DB::table('drip_enrollments')->where('sequence_id', $id)->where('status', 'active')
            ->whereNotExists(fn($q) => $q->select(DB::raw(1))->from('drip_sends')->whereColumn('drip_sends.enrollment_id', 'drip_enrollments.id'))
            ->update(['enrolled_at' => now(), 'updated_at' => now()]);
        Log::info('drip sequence status', ['sequence' => (int) $id, 'status' => $to, 'admin' => session('admin_name')]);
        return $this->back($id, "Sequence {$to}.");
    }

    public function sequenceSave(Request $r, $id)
    {
        $d = $r->validate(['name' => 'required|max:150', 'description' => 'nullable|max:2000']);
        DB::table('drip_sequences')->where('id', $id)->update($d + ['updated_at' => now()]);
        return $this->back($id, 'Sequence saved.');
    }

    public function sequenceCreate(Request $r)
    {
        $d = $r->validate(['name' => 'required|max:150', 'type' => 'required|in:' . implode(',', array_keys(Drip::TYPES))]);
        $trigger = ['signup' => 'verified_signup', 'winback' => 'segment', 'motivation' => 'workflow'][$d['type']] ?? 'manual';
        $id = DB::table('drip_sequences')->insertGetId($d + ['slug' => \Illuminate\Support\Str::slug($d['name']) . '-' . substr(uniqid(), -5), 'trigger' => $trigger, 'status' => 'PAUSED', 'created_at' => now(), 'updated_at' => now()]);
        return $this->back($id, 'Sequence created (PAUSED).');
    }

    public function stepSave(Request $r)
    {
        $d = $r->validate(['id' => 'nullable|integer', 'sequence_id' => 'required|integer|exists:drip_sequences,id', 'name' => 'required|max:150',
            'subject' => 'required|max:255', 'preview' => 'nullable|max:255', 'purpose' => 'nullable|max:255', 'body_html' => 'nullable',
            'day_offset' => 'required|integer|min:-365|max:3650', 'conditions' => 'nullable|array', 'conditions.*' => 'string|max:60', 'status' => 'required|in:DRAFT,ACTIVE,PAUSED']);
        $row = ['name' => $d['name'], 'subject' => $d['subject'], 'preview' => $d['preview'] ?? null, 'purpose' => $d['purpose'] ?? null,
            'body_html' => $d['body_html'] ?? '', 'day_offset' => $d['day_offset'], 'status' => $d['status'],
            'conditions' => json_encode(['all' => array_values($d['conditions'] ?? ['always'])]), 'updated_at' => now()];
        if (!empty($d['id'])) {
            DB::table('drip_steps')->where('id', $d['id'])->where('sequence_id', $d['sequence_id'])->update($row);
        } else {
            $row['position'] = (int) DB::table('drip_steps')->where('sequence_id', $d['sequence_id'])->max('position') + 1;
            DB::table('drip_steps')->insert($row + ['sequence_id' => $d['sequence_id'], 'created_at' => now()]);
        }
        return $this->back($d['sequence_id'], 'Email saved.');
    }

    public function stepDuplicate($id)
    {
        $s = (array) DB::table('drip_steps')->where('id', $id)->first(); abort_unless($s, 404);
        DB::table('drip_steps')->where('sequence_id', $s['sequence_id'])->where('position', '>', $s['position'])->increment('position');
        unset($s['id']); $s['position']++; $s['name'] .= ' (copy)'; $s['status'] = 'DRAFT'; $s['created_at'] = $s['updated_at'] = now();
        DB::table('drip_steps')->insert($s);
        return $this->back($s['sequence_id'], 'Email duplicated as DRAFT.');
    }

    public function stepDelete($id)
    {
        $s = DB::table('drip_steps')->where('id', $id)->first(); abort_unless($s, 404);
        DB::table('drip_steps')->where('id', $id)->delete();
        return $this->back($s->sequence_id, 'Email deleted (send history kept).');
    }

    public function stepStatus(Request $r, $id)
    {
        $s = DB::table('drip_steps')->where('id', $id)->first(); abort_unless($s, 404);
        $to = in_array($r->input('status'), ['ACTIVE', 'PAUSED', 'DRAFT']) ? $r->input('status') : 'PAUSED';
        DB::table('drip_steps')->where('id', $id)->update(['status' => $to, 'updated_at' => now()]);
        return $this->back($s->sequence_id, "Email set to {$to}.");
    }

    public function reorder(Request $r, $id)
    {
        foreach (array_values((array) $r->input('ids', [])) as $i => $sid) DB::table('drip_steps')->where('id', (int) $sid)->where('sequence_id', $id)->update(['position' => $i + 1]);
        return response()->json(['ok' => true]);
    }

    public function preview($id)
    {
        $s = DB::table('drip_steps')->where('id', $id)->first(); abort_unless($s, 404);
        [$subj, $html] = Drip::render($s, (object) ['email' => session('admin_email') ?: 'you@example.com', 'first_name' => 'Alex', 'user_id' => null]);
        return response($html)->header('Content-Security-Policy', "script-src 'none'");
    }

    /** Sends ONE test of a step to the signed-in admin's own email address only. */
    public function test($id)
    {
        $s = DB::table('drip_steps')->where('id', $id)->first(); abort_unless($s, 404);
        $to = strtolower(trim((string) session('admin_email')));
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return $this->back($s->sequence_id, 'Your admin account has no email address.');
        try {
            [$subj, $html] = Drip::render($s, (object) ['email' => $to, 'first_name' => session('admin_name'), 'user_id' => null]);
            Drip::deliver($to, '[TEST] ' . $subj, $html, null);
            Log::info('drip test sent', ['step' => (int) $id, 'admin' => session('admin_name')]);
            return $this->back($s->sequence_id, "Test sent to {$to}.");
        } catch (\Throwable $e) {
            Log::warning('drip test failed', ['step' => (int) $id, 'err' => mb_substr($e->getMessage(), 0, 200)]);
            return $this->back($s->sequence_id, 'Test failed: ' . mb_substr($e->getMessage(), 0, 150));
        }
    }

    /** Win-back (or other non-signup) enrollment by admin-selected segment. Sending still requires ACTIVE + kill switch on. */
    public function enrollSegment(Request $r, $id)
    {
        $seq = DB::table('drip_sequences')->where('id', $id)->first(); abort_unless($seq && $seq->type !== 'signup', 404);
        $d = $r->validate(['segment' => 'required|in:inactive_accounts,registered_between,emails', 'from' => 'nullable|date', 'to' => 'nullable|date', 'emails' => 'nullable|string|max:200000', 'confirm' => 'accepted']);
        $q = DB::table('wpgc_users')->whereNotNull('user_email')->where('user_email', '<>', '');
        if ($d['segment'] === 'inactive_accounts') $q->where('active', 0)->where('user_status', 1);
        if ($d['segment'] === 'registered_between') { $q->where('user_status', 1); if (!empty($d['from'])) $q->where('user_registered', '>=', $d['from']); if (!empty($d['to'])) $q->where('user_registered', '<=', $d['to'] . ' 23:59:59'); }
        if ($d['segment'] === 'emails') { $list = array_filter(array_map(fn($e) => strtolower(trim($e)), preg_split('/[\s,;]+/', (string) ($d['emails'] ?? '')))); $q->whereIn(DB::raw('LOWER(user_email)'), $list ?: ['-']); }
        $n = 0; $sup = DB::table('email_suppressions')->pluck('email')->flip();
        foreach ($q->limit(5000)->get(['ID', 'user_email', 'display_name', 'user_nicename']) as $u) {
            $email = strtolower(trim($u->user_email));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || isset($sup[$email])) continue;
            $n += DB::table('drip_enrollments')->insertOrIgnore(['sequence_id' => $id, 'user_id' => $u->ID, 'email' => $email, 'first_name' => Drip::firstName($u->display_name ?: $u->user_nicename),
                'source' => 'segment:' . $d['segment'], 'status' => 'active', 'enrolled_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        }
        Log::info('drip segment enroll', ['sequence' => (int) $id, 'segment' => $d['segment'], 'added' => $n, 'admin' => session('admin_name')]);
        return $this->back($id, "{$n} account(s) enrolled. Nothing sends while the sequence is PAUSED.");
    }
}
