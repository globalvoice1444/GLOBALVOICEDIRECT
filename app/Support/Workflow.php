<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 2026-11-02 Automation workflow engine. Runs on the drip:run tick (every 5 min).
 * Nothing runs unless DRIP_SENDING=true AND the workflow is ACTIVE. Emails use the drip library,
 * the shared per-person cap, suppression list and unsubscribe link.
 */
class Workflow
{
    const TRIGGERS = [
        'new_signup' => 'New signup (verified)',
        'payment_failed' => 'Payment failed',
        'no_login' => 'No login for X days',
        'new_number' => 'New number purchased',
        'manual' => 'Manual only',
    ];
    const ACTIONS = [
        'email' => 'Send email', 'wait' => 'Wait', 'branch' => 'If / else', 'tag' => 'Add / remove tag',
        'enroll_drip' => 'Enroll in drip sequence', 'notify' => 'Notify admin',
    ];
    const BRANCH_RULES = [
        'logged_in_since_enrolled' => 'Logged in since entering this workflow',
        'logged_in_within' => 'Logged in within the last N days',
        'has_tag' => 'Has tag',
        'has_number' => 'Has at least one phone number',
        'unsubscribed' => 'Unsubscribed from email',
    ];
    const DEMO_LOGINS = ['demobusiness'];
    const MAX_STEPS_PER_TICK = 10;

    public static function cfg($row): array { return json_decode((string) ($row->config ?? $row->trigger_config ?? ''), true) ?: []; }

    public static function recentlyEmailed(string $email, $since): bool
    {
        return DB::table('workflow_runs_log')->where('email', $email)->where('action', 'email')->where('result', 'ok')->where('created_at', '>=', $since)->exists()
            || DB::table('drip_sends')->where('email', $email)->where('status', 'sent')->where('sent_at', '>=', $since)->exists();
    }

    /** Ordered children of a path. */
    public static function path(int $wf, ?int $parent, string $branch)
    {
        return DB::table('workflow_nodes')->where('workflow_id', $wf)->where('branch', $branch)
            ->where(fn($q) => $parent ? $q->where('parent_id', $parent) : $q->whereNull('parent_id'))->orderBy('position')->orderBy('id')->get();
    }

    public static function first(int $wf, ?int $parent = null, string $branch = 'main'): ?object { return self::path($wf, $parent, $branch)->first(); }

    /** Next sibling in the same path; branch paths are terminal. */
    public static function next(object $node): ?object
    {
        foreach (self::path($node->workflow_id, $node->parent_id, $node->branch) as $n) if ($n->position > $node->position || ($n->position == $node->position && $n->id > $node->id)) return $n;
        return null;
    }

    /** Programmatic event hook, e.g. Workflow::fire('payment_failed', $userId). */
    public static function fire(string $trigger, int $userId, string $key = null): int
    {
        $n = 0;
        try {
            foreach (DB::table('workflows')->where('status', 'ACTIVE')->where('trigger', $trigger)->get() as $wf) $n += self::enroll($wf, $userId, $key ?: ($trigger . ':' . now()->format('YmdHi')));
        } catch (\Throwable $e) { Log::warning('workflow fire failed', ['t' => $trigger, 'err' => mb_substr($e->getMessage(), 0, 200)]); }
        return $n;
    }

    public static function enroll(object $wf, int $userId, string $cycle = 'once'): int
    {
        $u = DB::table('wpgc_users')->where('ID', $userId)->first(['ID', 'user_login', 'user_email', 'display_name', 'user_nicename']);
        if (!$u || in_array($u->user_login, self::DEMO_LOGINS, true)) return 0;
        $email = strtolower(trim((string) $u->user_email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 0;
        $first = self::first($wf->id);
        return DB::table('workflow_enrollments')->insertOrIgnore(['workflow_id' => $wf->id, 'user_id' => $u->ID, 'email' => $email,
            'first_name' => Drip::firstName($u->display_name ?: $u->user_nicename), 'cycle_key' => mb_substr($cycle, 0, 80),
            'status' => $first ? 'active' : 'completed', 'current_node_id' => $first->id ?? null, 'enrolled_at' => now(),
            'completed_at' => $first ? null : now(), 'created_at' => now(), 'updated_at' => now()]);
    }

    /** Find new trigger matches for ACTIVE workflows (only events after activation; no backfill). */
    public static function triggers(): int
    {
        $n = 0;
        foreach (DB::table('workflows')->where('status', 'ACTIVE')->get() as $wf) {
            $since = $wf->activated_at ?: now();
            $c = self::cfg((object) ['trigger_config' => $wf->trigger_config]);
            if ($wf->trigger === 'new_signup') {
                foreach (DB::table('wpgc_users')->where('user_status', 1)->where('user_registered', '>=', $since)->where('ID', '>', $wf->watermark)->orderBy('ID')->limit(200)->pluck('ID') as $id) {
                    $n += self::enroll($wf, $id); DB::table('workflows')->where('id', $wf->id)->update(['watermark' => $id]);
                }
            } elseif ($wf->trigger === 'new_number') {
                foreach (DB::table('user_phone_numbers')->where('created_at', '>=', $since)->where('id', '>', $wf->watermark)->orderBy('id')->limit(200)->get(['id', 'user_id']) as $r) {
                    if ($r->user_id) $n += self::enroll($wf, (int) $r->user_id, 'num:' . $r->id);
                    DB::table('workflows')->where('id', $wf->id)->update(['watermark' => $r->id]);
                }
            } elseif ($wf->trigger === 'no_login') {
                $days = max(1, (int) ($c['days'] ?? 7));
                $rows = DB::table('user_activity')->whereNotNull('last_login_at')->where('last_login_at', '<=', now()->subDays($days))
                    ->where('last_login_at', '>=', now()->subDays($days + 30))->limit(300)->get(['user_id', 'last_login_at']);
                foreach ($rows as $r) $n += self::enroll($wf, (int) $r->user_id, 'nl:' . $r->last_login_at);
            }
            // payment_failed: enrolled via Workflow::fire() — no automatic data source yet.
        }
        return $n;
    }

    public static function run(): array
    {
        $out = ['wf_enrolled' => 0, 'wf_steps' => 0, 'wf_emails' => 0, 'wf_held' => 0, 'wf_failed' => 0];
        if (!config('drip.enabled')) return $out;
        $active = DB::table('workflows')->where('status', 'ACTIVE')->pluck('id');
        if ($active->isEmpty()) return $out;
        $out['wf_enrolled'] = self::triggers();
        $budget = Drip::sendBudget();   // 2026-11-05 hourly cap + quota pause (shared)
        if ($budget <= 0) return $out;
        $capFrom = now()->subHours(config('drip.min_hours_between'));
        $enrs = DB::table('workflow_enrollments')->whereIn('workflow_id', $active)->where('status', 'active')
            ->where(fn($q) => $q->whereNull('wait_until')->orWhere('wait_until', '<=', now()))->orderBy('id')->limit(1000)->get();
        foreach ($enrs as $enr) {
            if ($budget <= 0) break;
            for ($i = 0; $i < self::MAX_STEPS_PER_TICK; $i++) {
                $node = $enr->current_node_id ? DB::table('workflow_nodes')->where('id', $enr->current_node_id)->first() : null;
                if (!$node) { self::finish($enr); break; }
                $r = self::exec($enr, $node, $capFrom);
                $out['wf_steps']++;
                if ($r === 'hold') { $out['wf_held']++; break; }
                if ($r === 'wait') break;
                if ($r === 'email') { $out['wf_emails']++; $budget--; }
                if ($r === 'failed') { $out['wf_failed']++; break; }
                if ($r === 'stop') break;
                if ($r === 'quota') { $budget = 0; break; }
                $enr = DB::table('workflow_enrollments')->where('id', $enr->id)->first();
                if (!$enr || $enr->status !== 'active' || $budget <= 0) break;
            }
        }
        DB::table('workflows')->whereIn('id', $active)->update(['last_run_at' => now()]);
        return $out;
    }

    private static function finish(object $enr, string $status = 'completed'): void
    {
        DB::table('workflow_enrollments')->where('id', $enr->id)->update(['status' => $status, 'current_node_id' => null, 'wait_until' => null, 'completed_at' => now(), 'updated_at' => now()]);
    }

    private static function advance(object $enr, ?object $to, array $extra = []): void
    {
        if (!$to) { self::finish($enr); return; }
        DB::table('workflow_enrollments')->where('id', $enr->id)->update(['current_node_id' => $to->id, 'wait_until' => null, 'updated_at' => now()] + $extra);
    }

    private static function log(object $enr, object $node, string $result, string $detail = '', ?string $claim = null): bool
    {
        return (bool) DB::table('workflow_runs_log')->insertOrIgnore(['workflow_id' => $enr->workflow_id, 'enrollment_id' => $enr->id, 'node_id' => $node->id,
            'action' => $node->type, 'result' => $result, 'detail' => mb_substr($detail, 0, 255), 'email' => $enr->email, 'claim' => $claim, 'created_at' => now()]);
    }

    public static function branchPasses(object $enr, array $c): bool
    {
        $uid = (int) $enr->user_id;
        switch ($c['rule'] ?? '') {
            case 'logged_in_since_enrolled': return DB::table('user_activity')->where('user_id', $uid)->where('last_login_at', '>', $enr->enrolled_at)->exists();
            case 'logged_in_within': return DB::table('user_activity')->where('user_id', $uid)->where('last_login_at', '>=', now()->subDays(max(1, (int) ($c['days'] ?? 7))))->exists();
            case 'has_tag': return DB::table('user_tags')->where('user_id', $uid)->where('tag', (string) ($c['tag'] ?? ''))->exists();
            case 'has_number': return DB::table('user_phone_numbers')->where('user_id', $uid)->exists();
            case 'unsubscribed': return Drip::suppressed($enr->email);
        }
        return false;
    }

    /** Returns next|wait|hold|email|failed|stop */
    private static function exec(object $enr, object $node, $capFrom): string
    {
        $c = self::cfg($node);
        switch ($node->type) {
            case 'wait':
                $ctx = json_decode((string) $enr->context, true) ?: [];
                if (($ctx['waited'] ?? 0) == $node->id) { $ctx['waited'] = 0; self::advance($enr, self::next($node), ['context' => json_encode($ctx)]); return 'next'; }
                $mins = max(1, (int) ($c['amount'] ?? 1)) * (['minutes' => 1, 'hours' => 60, 'days' => 1440][$c['unit'] ?? 'days'] ?? 1440);
                $ctx['waited'] = $node->id;
                DB::table('workflow_enrollments')->where('id', $enr->id)->update(['wait_until' => now()->addMinutes($mins), 'context' => json_encode($ctx), 'updated_at' => now()]);
                self::log($enr, $node, 'waiting', "{$mins} min");
                return 'wait';
            case 'branch':
                $yes = self::branchPasses($enr, $c);
                self::log($enr, $node, 'ok', $yes ? 'yes' : 'no');
                self::advance($enr, self::first($enr->workflow_id, $node->id, $yes ? 'yes' : 'no'));
                return 'next';
            case 'tag':
                $tag = mb_substr(trim((string) ($c['tag'] ?? '')), 0, 60);
                if ($tag !== '' && $enr->user_id) {
                    if (($c['op'] ?? 'add') === 'remove') DB::table('user_tags')->where('user_id', $enr->user_id)->where('tag', $tag)->delete();
                    else DB::table('user_tags')->insertOrIgnore(['user_id' => $enr->user_id, 'tag' => $tag, 'source' => 'workflow:' . $enr->workflow_id, 'created_at' => now(), 'updated_at' => now()]);
                }
                self::log($enr, $node, 'ok', ($c['op'] ?? 'add') . ' ' . $tag);
                self::advance($enr, self::next($node)); return 'next';
            case 'notify':
                $u = DB::table('wpgc_users')->where('ID', $enr->user_id)->first(['user_login', 'display_name', 'user_email', 'user_registered']);
                $title = strtr((string) ($c['title'] ?? 'Workflow follow-up'), ['{{first_name}}' => $enr->first_name ?: '', '{{email}}' => $enr->email]);
                \App\Support\AdminAlerts::log('workflow', (string) ($c['severity'] ?? 'info'), mb_substr($title, 0, 190), [
                    'workflow' => (int) $enr->workflow_id, 'task' => (string) ($c['task'] ?? ''), 'customer' => $u->display_name ?? '', 'login' => $u->user_login ?? '',
                    'email' => $enr->email, 'registered' => $u->user_registered ?? '', 'due' => !empty($c['due_minutes']) ? now()->addMinutes((int) $c['due_minutes'])->toDateTimeString() : null,
                ], (int) $enr->user_id);
                self::log($enr, $node, 'ok', $title);
                self::advance($enr, self::next($node)); return 'next';
            case 'enroll_drip':
                $seq = DB::table('drip_sequences')->where('id', (int) ($c['sequence_id'] ?? 0))->first();
                if ($seq && !Drip::suppressed($enr->email)) DB::table('drip_enrollments')->insertOrIgnore(['sequence_id' => $seq->id, 'user_id' => $enr->user_id, 'email' => $enr->email,
                    'first_name' => $enr->first_name, 'source' => 'workflow:' . $enr->workflow_id, 'status' => 'active', 'enrolled_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
                self::log($enr, $node, $seq ? 'ok' : 'skipped', $seq ? 'enrolled in ' . $seq->name : 'sequence missing');
                self::advance($enr, self::next($node)); return 'next';
            case 'email':
                $step = DB::table('drip_steps')->where('id', (int) ($c['step_id'] ?? 0))->first();
                if (!$step || $step->status !== 'ACTIVE') { self::log($enr, $node, 'skipped', $step ? 'email not ACTIVE in library' : 'email missing'); self::advance($enr, self::next($node)); return 'next'; }
                if (Drip::suppressed($enr->email)) { self::log($enr, $node, 'skipped', 'unsubscribed'); self::finish($enr, 'stopped'); return 'stop'; }
                if (self::recentlyEmailed($enr->email, $capFrom)) return 'hold'; // shared 20h cap: retry on a later tick
                $claim = 'e' . $enr->id . '-n' . $node->id . '-' . substr(md5((string) $enr->enrolled_at), 0, 6);
                if (!self::log($enr, $node, 'sending', $step->subject, $claim)) { self::advance($enr, self::next($node)); return 'next'; }
                try {
                    [$subj, $html, $unsub] = Drip::render($step, $enr);
                    $html = Tracking::apply($html, "workflow", (int) DB::table("workflow_runs_log")->where("claim", $claim)->value("id"), (int) $enr->workflow_id, $enr->email); // trk
                    Drip::deliver($enr->email, $subj, $html, $unsub);
                    DB::table('workflow_runs_log')->where('claim', $claim)->update(['result' => 'ok']);
                    self::advance($enr, self::next($node)); return 'email';
                } catch (\Throwable $e) {
                    if (Drip::isQuotaError($e)) { DB::table('workflow_runs_log')->where('claim', $claim)->where('result', 'sending')->delete(); return 'quota'; }   // 2026-11-05 retry same node after pause
                    DB::table('workflow_runs_log')->where('claim', $claim)->update(['result' => 'failed', 'detail' => mb_substr($e->getMessage(), 0, 250)]);
                    self::advance($enr, self::next($node)); return 'failed';
                }
        }
        self::log($enr, $node, 'skipped', 'unknown step type');
        self::advance($enr, self::next($node)); return 'next';
    }
}
