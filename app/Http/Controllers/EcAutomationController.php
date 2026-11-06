<?php

namespace App\Http\Controllers;

use App\Portal\Demo;
use App\Support\CustomerEmail as CE;
use App\Support\CustomerSequences as CS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** 2026-11-02 Customer panel: Sequences + Automations. Scoped to session user_id. New items always start PAUSED. */
class EcAutomationController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store'];
    private function uid(): int { $u = (int) session()->get('user_id'); abort_unless($u > 0, 403); return $u; }
    private function j($d, int $s = 200) { return response()->json($d, $s, self::NC); }
    private function err(string $m, int $s = 422) { return $this->j(['error' => $m], $s); }
    private function seq(int $id) { return DB::table('ec_sequences')->where('id', $id)->where('user_id', $this->uid())->first(); }

    public static function data(int $uid, bool $demo): array
    {
        $st = CS::starters();
        $meta = array_map(fn ($s) => ['name' => $s[0], 'desc' => $s[1], 'steps' => count($s[2])], $st);
        if ($demo) {
            $mk = fn ($k, $id, $status, $en, $sent) => ['id' => $id, 'name' => $st[$k][0], 'status' => $status, 'enrolled' => $en, 'active' => (int) ($en / 2), 'sent' => $sent, 'failed' => 0,
                'steps' => array_map(fn ($x, $i) => ['id' => $id * 10 + $i, 'position' => $i + 1, 'day_offset' => $x[0], 'subject' => $x[1], 'preview' => null, 'body_html' => $x[2]], $st[$k][2], array_keys($st[$k][2]))];
            return ['sequences' => [$mk('welcome', 701, 'ACTIVE', 12, 28), $mk('followup', 702, 'PAUSED', 0, 0)],
                'automations' => [['id' => 801, 'name' => 'New contacts get the welcome series', 'status' => 'ACTIVE', 'trigger' => 'contact_added', 'trigger_tag' => null, 'action' => 'enroll', 'action_value' => '701', 'runs' => 12, 'last_run_at' => now()->subDay()->toDateTimeString()],
                    ['id' => 802, 'name' => 'Quote sent → follow-up', 'status' => 'PAUSED', 'trigger' => 'tag_added', 'trigger_tag' => 'quote-sent', 'action' => 'enroll', 'action_value' => '702', 'runs' => 0, 'last_run_at' => null]],
                'starters' => $meta, 'triggers' => CS::TRIGGERS, 'actions' => CS::ACTIONS];
        }
        $seqs = DB::table('ec_sequences')->where('user_id', $uid)->orderByDesc('id')->get()->map(function ($s) {
            $a = (array) $s;
            $a['steps'] = DB::table('ec_seq_steps')->where('sequence_id', $s->id)->orderBy('position')->get(['id', 'position', 'day_offset', 'subject', 'preview', 'body_html'])->all();
            $e = DB::table('ec_seq_enrollments')->where('sequence_id', $s->id)->selectRaw("COUNT(*) n, SUM(status='active') a")->first();
            $x = DB::table('ec_seq_sends as x')->join('ec_seq_enrollments as e', 'e.id', '=', 'x.enrollment_id')->where('e.sequence_id', $s->id)->selectRaw("SUM(x.status='sent') s, SUM(x.status='failed') f")->first();
            return $a + ['enrolled' => (int) $e->n, 'active' => (int) $e->a, 'sent' => (int) $x->s, 'failed' => (int) $x->f];
        })->all();
        return ['sequences' => $seqs, 'automations' => DB::table('ec_automations')->where('user_id', $uid)->orderByDesc('id')->get()->map(fn ($x) => (array) $x)->all(),
            'starters' => $meta, 'triggers' => CS::TRIGGERS, 'actions' => CS::ACTIONS];
    }

    // ---------- sequences ----------
    public function seqCreate(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid();
        if (DB::table('ec_sequences')->where('user_id', $uid)->count() >= 50) return $this->err('You can have up to 50 sequences.');
        if ($k = (string) $r->input('starter')) { $id = CS::createFromStarter($uid, $k); if (!$id) return $this->err('Unknown starter.'); return $this->j(['ok' => true, 'id' => $id]); }
        $name = mb_substr(trim(strip_tags((string) $r->input('name'))), 0, 150) ?: 'New sequence';
        return $this->j(['ok' => true, 'id' => DB::table('ec_sequences')->insertGetId(['user_id' => $uid, 'name' => $name, 'status' => 'PAUSED', 'created_at' => now(), 'updated_at' => now()])]);
    }

    public function seqUpdate(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid(); $s = $this->seq((int) $r->input('id')); if (!$s) return $this->err('Sequence not found.', 404);
        $u = fn ($x) => DB::table('ec_sequences')->where('id', $s->id)->where('user_id', $uid)->update($x + ['updated_at' => now()]);
        switch ((string) $r->input('action')) {
            case 'rename': $u(['name' => mb_substr(trim(strip_tags((string) $r->input('name'))), 0, 150) ?: $s->name]); break;
            case 'activate':
                if (!CE::smtp($uid)) return $this->j(['error' => 'Connect your email first. Sequences are sent from your own mailbox.', 'connect' => true], 422);
                if ($b = CE::blocked($uid)) return $this->err($b);
                if (!DB::table('ec_seq_steps')->where('sequence_id', $s->id)->exists()) return $this->err('Add at least one email first.');
                foreach (DB::table('ec_seq_steps')->where('sequence_id', $s->id)->get() as $st) if (str_contains($st->body_html, 'href="https://"') || str_contains($st->body_html, 'Write your')) return $this->err('Email ' . $st->position . ' still has placeholder text or an empty button link. Edit it first.');
                $u(['status' => 'ACTIVE']); break;
            case 'pause': $u(['status' => 'PAUSED']); break;
            case 'delete':
                $en = DB::table('ec_seq_enrollments')->where('sequence_id', $s->id)->pluck('id');
                DB::table('ec_seq_sends')->whereIn('enrollment_id', $en)->delete(); DB::table('ec_seq_enrollments')->where('sequence_id', $s->id)->delete();
                DB::table('ec_seq_steps')->where('sequence_id', $s->id)->delete(); DB::table('ec_sequences')->where('id', $s->id)->where('user_id', $uid)->delete();
                DB::table('ec_automations')->where('user_id', $uid)->whereIn('action', ['enroll', 'stop'])->where('action_value', (string) $s->id)->update(['status' => 'PAUSED']); break;
            case 'enroll':
                $tag = CE::normTags((string) $r->input('tag'));
                $q = DB::table('ec_contacts')->where('user_id', $uid)->where('status', 'active'); if ($tag) $q->whereRaw('FIND_IN_SET(?, tags)', [$tag]);
                $n = CS::enroll($uid, $s->id, $q->limit(25000)->pluck('id')->all(), 'manual');
                return $this->j(['ok' => true, 'enrolled' => $n, 'paused' => $s->status !== 'ACTIVE']);
            default: return $this->err('Unknown action.');
        }
        return $this->j(['ok' => true]);
    }

    public function stepSave(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid(); $s = $this->seq((int) $r->input('sequence_id')); if (!$s) return $this->err('Sequence not found.', 404);
        $subj = mb_substr(trim(strip_tags((string) $r->input('subject'))), 0, 200); $body = CE::sanitize((string) $r->input('body_html'));
        if ($subj === '') return $this->err('Add a subject line.'); if (trim(strip_tags($body, '<img>')) === '') return $this->err('Write the email.');
        $row = ['day_offset' => max(0, min(365, (int) $r->input('day_offset'))), 'subject' => $subj, 'preview' => mb_substr(trim(strip_tags((string) $r->input('preview'))), 0, 200) ?: null, 'body_html' => $body, 'updated_at' => now()];
        $id = (int) $r->input('id');
        if ($id) DB::table('ec_seq_steps')->where('id', $id)->where('sequence_id', $s->id)->update($row);
        else { if (DB::table('ec_seq_steps')->where('sequence_id', $s->id)->count() >= 20) return $this->err('Up to 20 emails per sequence.');
            $id = DB::table('ec_seq_steps')->insertGetId($row + ['sequence_id' => $s->id, 'position' => (int) DB::table('ec_seq_steps')->where('sequence_id', $s->id)->max('position') + 1, 'created_at' => now()]); }
        // keep order by day
        foreach (DB::table('ec_seq_steps')->where('sequence_id', $s->id)->orderBy('day_offset')->orderBy('id')->pluck('id') as $i => $sid) DB::table('ec_seq_steps')->where('id', $sid)->update(['position' => $i + 1]);
        return $this->j(['ok' => true, 'id' => $id]);
    }

    public function stepDelete(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $s = $this->seq((int) $r->input('sequence_id')); if (!$s) return $this->err('Sequence not found.', 404);
        DB::table('ec_seq_steps')->where('id', (int) $r->input('id'))->where('sequence_id', $s->id)->delete();
        return $this->j(['ok' => true]);
    }

    // ---------- automations ----------
    public function autoSave(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid();
        $trig = (string) $r->input('trigger'); $act = (string) $r->input('action');
        if (!isset(CS::TRIGGERS[$trig])) return $this->err('Pick a trigger.'); if (!isset(CS::ACTIONS[$act])) return $this->err('Pick an action.');
        $ttag = $trig === 'tag_added' ? CE::normTags((string) $r->input('trigger_tag')) : null;
        if ($trig === 'tag_added' && !$ttag) return $this->err('Enter the tag that starts this automation.');
        $val = (string) $r->input('action_value');
        if (in_array($act, ['enroll', 'stop'], true)) { if (!$this->seq((int) $val)) return $this->err('Pick one of your sequences.'); $val = (string) (int) $val; }
        else { $val = CE::normTags($val); if (!$val) return $this->err('Enter the tag.'); if ($act === 'add_tag' && $val === $ttag) return $this->err('That would loop. Use a different tag.'); }
        $name = mb_substr(trim(strip_tags((string) $r->input('name'))), 0, 150) ?: (CS::TRIGGERS[$trig] . ' → ' . CS::ACTIONS[$act]);
        $row = ['name' => $name, 'trigger' => $trig, 'trigger_tag' => $ttag, 'action' => $act, 'action_value' => $val, 'updated_at' => now()];
        $id = (int) $r->input('id');
        if ($id) { if (!DB::table('ec_automations')->where('id', $id)->where('user_id', $uid)->update($row)) return $this->err('Automation not found.', 404); }
        else { if (DB::table('ec_automations')->where('user_id', $uid)->count() >= 50) return $this->err('Up to 50 automations.'); $id = DB::table('ec_automations')->insertGetId($row + ['user_id' => $uid, 'status' => 'PAUSED', 'created_at' => now()]); }
        return $this->j(['ok' => true, 'id' => $id]);
    }

    public function autoUpdate(Request $r)
    {
        if (Demo::on()) return Demo::readonly();
        $uid = $this->uid(); $q = fn () => DB::table('ec_automations')->where('id', (int) $r->input('id'))->where('user_id', $uid);
        if (!$q()->exists()) return $this->err('Automation not found.', 404);
        switch ((string) $r->input('action')) {
            case 'activate': $q()->update(['status' => 'ACTIVE', 'updated_at' => now()]); break;
            case 'pause': $q()->update(['status' => 'PAUSED', 'updated_at' => now()]); break;
            case 'delete': $q()->delete(); break;
            default: return $this->err('Unknown action.');
        }
        return $this->j(['ok' => true]);
    }
}
