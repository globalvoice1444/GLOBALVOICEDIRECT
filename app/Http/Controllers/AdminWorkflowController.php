<?php

namespace App\Http\Controllers;

use App\Support\Drip;
use App\Support\Workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/** Admin > Automations > Workflows (route group: adminauth). 2026-11-02 */
class AdminWorkflowController extends Controller
{
    const TEMPLATES = [
        'welcome' => ['Welcome + personal voice note', 'new_signup', [], 'On signup: alert the team to send a personal voice note within 1 hour, tag the customer and check in after 3 days.', [
            ['type' => 'notify', 'config' => ['title' => 'Send a voice note to {{first_name}} within 1 hour', 'task' => 'New signup: send a personal welcome voice note within the first hour (do not call or text from the system).', 'severity' => 'warning', 'due_minutes' => 60]],
            ['type' => 'tag', 'config' => ['op' => 'add', 'tag' => 'new-signup']],
            ['type' => 'wait', 'config' => ['amount' => 3, 'unit' => 'days']],
            ['type' => 'branch', 'config' => ['rule' => 'logged_in_since_enrolled'], 'yes' => [['type' => 'tag', 'config' => ['op' => 'add', 'tag' => 'engaged']]],
                'no' => [['type' => 'notify', 'config' => ['title' => '{{first_name}} has not logged in 3 days after signup', 'task' => 'Offer a personal setup call.', 'severity' => 'info']]]],
        ]],
        'inactive' => ['Inactive 7 days → Daily Motivation', 'no_login', ['days' => 7], 'Customers who have not logged in for 7 days get the Daily Motivation sequence.', [
            ['type' => 'tag', 'config' => ['op' => 'add', 'tag' => 'inactive-7d']],
            ['type' => 'enroll_drip', 'config' => ['sequence_slug' => 'daily-motivation']],
        ]],
        'number' => ['New number follow-up', 'new_number', [], 'One day after a number is added, send the "business line everywhere" tip.', [
            ['type' => 'wait', 'config' => ['amount' => 1, 'unit' => 'days']],
            ['type' => 'email', 'config' => ['step_name' => 'Email 3 · Business line everywhere']],
        ]],
        'payment' => ['Failed payment recovery', 'payment_failed', [], 'Flag billing risk and alert the team. (Needs a payment-failure data source before it can trigger.)', [
            ['type' => 'tag', 'config' => ['op' => 'add', 'tag' => 'billing-risk']],
            ['type' => 'notify', 'config' => ['title' => 'Payment failed for {{first_name}} ({{email}})', 'task' => 'Reach out about the payment method.', 'severity' => 'warning', 'due_minutes' => 1440]],
        ]],
    ];

    public function index(Request $r)
    {
        $wfs = DB::table('workflows')->orderBy('id')->get();
        $enr = DB::table('workflow_enrollments')->selectRaw("workflow_id, COUNT(*) total, SUM(status='active') active, SUM(status='completed') done")->groupBy('workflow_id')->get()->keyBy('workflow_id');
        $emails30 = DB::table('workflow_runs_log')->where('action', 'email')->where('result', 'ok')->where('created_at', '>=', now()->subDays(30))->count();
        $recent = DB::table('workflow_runs_log')->orderByDesc('id')->limit(25)->get();
        $tracked = DB::table('user_activity')->count();
        return view('admin.workflows', ['wfs' => $wfs, 'enr' => $enr, 'emails30' => $emails30, 'recent' => $recent, 'tracked' => $tracked,
            'enabled' => (bool) config('drip.enabled'), 'triggers' => Workflow::TRIGGERS, 'templates' => self::TEMPLATES, 'tab' => $r->query('tab', 'list')]);
    }

    public function create(Request $r)
    {
        $t = self::TEMPLATES[$r->input('template')] ?? null;
        $id = DB::table('workflows')->insertGetId(['name' => $t[0] ?? (mb_substr((string) $r->input('name'), 0, 150) ?: 'Untitled workflow'), 'trigger' => $t[1] ?? 'manual',
            'trigger_config' => json_encode($t[2] ?? new \stdClass), 'description' => $t[3] ?? null, 'status' => 'PAUSED', 'created_at' => now(), 'updated_at' => now()]);
        if ($t) self::insertTree($id, $t[4]);
        return redirect('/admin/automations/workflows/' . $id)->with('message', 'Workflow created (PAUSED).');
    }

    /** Insert template nodes, resolving library references by slug/name. */
    public static function insertTree(int $wf, array $nodes, ?int $parent = null, string $branch = 'main'): void
    {
        foreach (array_values($nodes) as $i => $n) {
            $c = $n['config'] ?? [];
            if (isset($c['sequence_slug'])) { $c['sequence_id'] = (int) DB::table('drip_sequences')->where('slug', $c['sequence_slug'])->value('id'); unset($c['sequence_slug']); }
            if (isset($c['step_name'])) { $c['step_id'] = (int) DB::table('drip_steps')->where('name', $c['step_name'])->value('id'); unset($c['step_name']); }
            $id = DB::table('workflow_nodes')->insertGetId(['workflow_id' => $wf, 'parent_id' => $parent, 'branch' => $branch, 'position' => $i + 1, 'type' => $n['type'],
                'config' => json_encode($c ?: new \stdClass), 'created_at' => now(), 'updated_at' => now()]);
            if ($n['type'] === 'branch') { self::insertTree($wf, $n['yes'] ?? [], $id, 'yes'); self::insertTree($wf, $n['no'] ?? [], $id, 'no'); }
        }
    }

    private static function tree(int $wf, ?int $parent = null, string $branch = 'main'): array
    {
        return Workflow::path($wf, $parent, $branch)->map(function ($n) use ($wf) {
            $o = ['id' => $n->id, 'type' => $n->type, 'config' => json_decode((string) $n->config, true) ?: new \stdClass];
            if ($n->type === 'branch') { $o['yes'] = self::tree($wf, $n->id, 'yes'); $o['no'] = self::tree($wf, $n->id, 'no'); }
            return $o;
        })->all();
    }

    public function show($id)
    {
        $wf = DB::table('workflows')->where('id', $id)->first(); abort_unless($wf, 404);
        $lib = DB::table('drip_steps')->join('drip_sequences', 'drip_sequences.id', '=', 'drip_steps.sequence_id')
            ->orderBy('drip_sequences.id')->orderBy('drip_steps.position')->get(['drip_steps.id', 'drip_steps.name', 'drip_steps.subject', 'drip_steps.status', 'drip_sequences.name as seq']);
        return view('admin.workflow-builder', ['wf' => $wf, 'tree' => self::tree($wf->id), 'lib' => $lib, 'seqs' => DB::table('drip_sequences')->orderBy('id')->get(['id', 'name', 'status']),
            'triggers' => Workflow::TRIGGERS, 'rules' => Workflow::BRANCH_RULES, 'enabled' => (bool) config('drip.enabled'),
            'log' => DB::table('workflow_runs_log')->where('workflow_id', $wf->id)->orderByDesc('id')->limit(20)->get(),
            'enr' => DB::table('workflow_enrollments')->where('workflow_id', $wf->id)->selectRaw("COUNT(*) total, SUM(status='active') active")->first()]);
    }

    private static function clean(array $nodes, int $depth = 0): array
    {
        $ok = array_keys(Workflow::ACTIONS); $out = [];
        foreach (array_slice($nodes, 0, 40) as $n) {
            if (!is_array($n) || !in_array($n['type'] ?? '', $ok, true)) continue;
            $c = is_array($n['config'] ?? null) ? $n['config'] : [];
            $c = array_map(fn($v) => is_scalar($v) ? mb_substr((string) $v, 0, 500) : '', array_slice($c, 0, 10, true));
            $o = ['id' => (int) ($n['id'] ?? 0), 'type' => $n['type'], 'config' => $c];
            if ($n['type'] === 'branch') {
                if ($depth >= 3) continue;
                $o['yes'] = self::clean((array) ($n['yes'] ?? []), $depth + 1); $o['no'] = self::clean((array) ($n['no'] ?? []), $depth + 1);
            }
            $out[] = $o;
        }
        return $out;
    }

    private static function saveTree(int $wf, array $nodes, ?int $parent, string $branch, array &$keep): void
    {
        foreach ($nodes as $i => $n) {
            $row = ['workflow_id' => $wf, 'parent_id' => $parent, 'branch' => $branch, 'position' => $i + 1, 'type' => $n['type'], 'config' => json_encode($n['config'] ?: new \stdClass), 'updated_at' => now()];
            $id = $n['id'] && DB::table('workflow_nodes')->where('id', $n['id'])->where('workflow_id', $wf)->exists() ? $n['id'] : 0;
            if ($id) DB::table('workflow_nodes')->where('id', $id)->update($row); else $id = DB::table('workflow_nodes')->insertGetId($row + ['created_at' => now()]);
            $keep[] = $id;
            if ($n['type'] === 'branch') { self::saveTree($wf, $n['yes'], $id, 'yes', $keep); self::saveTree($wf, $n['no'], $id, 'no', $keep); }
        }
    }

    public function save(Request $r, $id)
    {
        $wf = DB::table('workflows')->where('id', $id)->first(); abort_unless($wf, 404);
        $d = $r->validate(['name' => 'required|max:150', 'trigger' => 'required|in:' . implode(',', array_keys(Workflow::TRIGGERS)), 'days' => 'nullable|integer|min:1|max:365', 'tree' => 'required|string|max:200000']);
        $tree = self::clean(json_decode($d['tree'], true) ?: []);
        DB::transaction(function () use ($wf, $d, $tree) {
            DB::table('workflows')->where('id', $wf->id)->update(['name' => $d['name'], 'trigger' => $d['trigger'], 'trigger_config' => json_encode(['days' => (int) ($d['days'] ?? 7)]), 'updated_at' => now()]);
            $keep = []; self::saveTree($wf->id, $tree, null, 'main', $keep);
            DB::table('workflow_nodes')->where('workflow_id', $wf->id)->whereNotIn('id', $keep ?: [0])->delete();
            // enrollments sitting on a removed step stop cleanly
            DB::table('workflow_enrollments')->where('workflow_id', $wf->id)->where('status', 'active')->whereNotIn('current_node_id', $keep ?: [0])
                ->update(['status' => 'stopped', 'current_node_id' => null, 'completed_at' => now(), 'updated_at' => now()]);
        });
        return response()->json(['ok' => true]);
    }

    public function status(Request $r, $id)
    {
        $wf = DB::table('workflows')->where('id', $id)->first(); abort_unless($wf, 404);
        $to = $r->input('status') === 'ACTIVE' ? 'ACTIVE' : 'PAUSED';
        $upd = ['status' => $to, 'updated_at' => now()];
        if ($to === 'ACTIVE') { $upd['activated_at'] = now(); if (in_array($wf->trigger, ['new_signup', 'new_number'], true)) $upd['watermark'] = (int) ($wf->trigger === 'new_signup' ? DB::table('wpgc_users')->max('ID') : DB::table('user_phone_numbers')->max('id')); }
        DB::table('workflows')->where('id', $id)->update($upd);
        Log::info('workflow status', ['workflow' => (int) $id, 'status' => $to, 'admin' => session('admin_name')]);
        return redirect('/admin/automations/workflows/' . $id)->with('message', "Workflow {$to}." . ($to === 'ACTIVE' ? ' Only events from now on are picked up.' : ''));
    }

    public function delete($id)
    {
        $wf = DB::table('workflows')->where('id', $id)->first(); abort_unless($wf && $wf->status === 'PAUSED', 404);
        DB::table('workflow_nodes')->where('workflow_id', $id)->delete();
        DB::table('workflow_enrollments')->where('workflow_id', $id)->where('status', 'active')->update(['status' => 'stopped', 'updated_at' => now()]);
        DB::table('workflows')->where('id', $id)->delete();
        return redirect('/admin/automations/workflows')->with('message', 'Workflow deleted.');
    }

    /** Sends every email step in this workflow (main path + branches) to the signed-in admin only. Max 6. */
    public function test($id)
    {
        $to = strtolower(trim((string) session('admin_email')));
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return back()->with('message', 'Your admin account has no email address.');
        $ids = DB::table('workflow_nodes')->where('workflow_id', $id)->where('type', 'email')->orderBy('id')->limit(6)->get()->map(fn($n) => (int) (Workflow::cfg($n)['step_id'] ?? 0))->filter();
        $n = 0;
        foreach (DB::table('drip_steps')->whereIn('id', $ids)->get() as $step) {
            [$s, $h] = Drip::render($step, (object) ['email' => $to, 'first_name' => session('admin_name'), 'user_id' => null]);
            Drip::deliver($to, '[TEST] ' . $s, $h); $n++;
        }
        return back()->with('message', $n ? "Sent $n test email(s) to $to." : 'This workflow has no email steps to test.');
    }
}
