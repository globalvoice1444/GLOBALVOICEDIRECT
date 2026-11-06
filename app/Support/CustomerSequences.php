<?php

namespace App\Support;

use App\Support\CustomerEmail as CE;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/** 2026-11-02 Customer sequences + automations engine. Sends only through the customer's own connected mailbox; shares the per-customer daily limit. */
class CustomerSequences
{
    const TRIGGERS = ['contact_added' => 'A contact is added', 'tag_added' => 'A tag is added to a contact'];
    const ACTIONS = ['enroll' => 'Start a sequence', 'add_tag' => 'Add a tag', 'remove_tag' => 'Remove a tag', 'stop' => 'Stop a sequence'];

    public static function starters(): array
    {
        $b = fn ($t) => '<p style="margin:22px 0"><a href="https://" style="display:inline-block;background:#1E2A36;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 22px;border-radius:8px">' . $t . '</a></p>';
        return [
            'welcome' => ['Welcome series', 'New customers or leads get 3 friendly emails over a week.', [
                [0, 'Welcome to {{my_business}}, {{first_name}}!', '<p>Hi {{first_name}},</p><p>Thanks for choosing {{my_business}}. We are glad to have {{business_name}} with us.</p><p>Here is what you can expect from us, and how to reach us anytime.</p><p>Just reply to this email with any question.</p>'],
                [2, 'A quick tip to get the most from us', '<p>Hi {{first_name}},</p><p>Here is one thing most of our customers wish they knew sooner:</p><p><b>Write your best tip here.</b></p>' . $b('Learn more')],
                [6, 'How are we doing, {{first_name}}?', '<p>Hi {{first_name}},</p><p>It has been a week. How is everything going? If you have a minute, a quick review means a lot to a small business like ours.</p>' . $b('Leave a review')],
            ]],
            'followup' => ['Quote follow-up', 'Follow up after you send a quote or estimate.', [
                [1, 'Any questions about your quote?', '<p>Hi {{first_name}},</p><p>I wanted to make sure you received the quote from {{my_business}}. Happy to walk you through it.</p><p>Just reply to this email.</p>'],
                [4, 'Still interested, {{first_name}}?', '<p>Hi {{first_name}},</p><p>Just checking in on the quote for {{business_name}}. If timing or budget changed, let me know and we will find an option that works.</p>'],
                [10, 'Closing the loop', '<p>Hi {{first_name}},</p><p>I will close out your quote for now. If you want to pick it back up, simply reply and we will get started.</p>'],
            ]],
            'winback' => ['Win-back', 'Re-engage customers you have not heard from in a while.', [
                [0, 'We miss you at {{my_business}}', '<p>Hi {{first_name}},</p><p>It has been a while! Here is what is new at {{my_business}}.</p>' . $b('See what is new')],
                [5, 'A little something to welcome you back', '<p>Hi {{first_name}},</p><p>As a thank-you for being a past customer, here is a special offer just for you.</p><p style="font-size:20px;font-weight:bold;color:#FF5000">Write your offer here</p>' . $b('Claim my offer')],
            ]],
        ];
    }

    /** Create a sequence from a starter (always PAUSED). */
    public static function createFromStarter(int $uid, string $key): ?int
    {
        $s = self::starters()[$key] ?? null; if (!$s) return null;
        $id = DB::table('ec_sequences')->insertGetId(['user_id' => $uid, 'name' => $s[0], 'status' => 'PAUSED', 'starter' => $key, 'created_at' => now(), 'updated_at' => now()]);
        foreach ($s[2] as $i => $st) DB::table('ec_seq_steps')->insert(['sequence_id' => $id, 'position' => $i + 1, 'day_offset' => $st[0], 'subject' => $st[1], 'body_html' => CE::sanitize($st[2]), 'created_at' => now(), 'updated_at' => now()]);
        return $id;
    }

    public static function enroll(int $uid, int $seqId, array $contactIds, string $src): int
    {
        if (!DB::table('ec_sequences')->where('id', $seqId)->where('user_id', $uid)->exists()) return 0;
        $n = 0;
        foreach (array_chunk($contactIds, 500) as $ch) {
            $rows = DB::table('ec_contacts')->where('user_id', $uid)->whereIn('id', $ch)->where('status', 'active')->get(['id', 'email']);
            $n += DB::table('ec_seq_enrollments')->insertOrIgnore($rows->map(fn ($c) => ['sequence_id' => $seqId, 'contact_id' => $c->id, 'email' => $c->email, 'status' => 'active', 'enrolled_at' => now(), 'source' => $src, 'created_at' => now(), 'updated_at' => now()])->all());
        }
        return $n;
    }

    /** Fire ACTIVE automations for an event. Called from contact add / import / tag changes. Never throws. */
    public static function fire(int $uid, string $event, array $contactIds, ?string $tag = null, int $depth = 0): void
    {
        if (!$contactIds || $depth > 2) return;
        try {
            $q = DB::table('ec_automations')->where('user_id', $uid)->where('status', 'ACTIVE')->where('trigger', $event);
            foreach ($q->get() as $a) {
                if ($event === 'tag_added') { $tags = array_filter(explode(',', (string) $tag)); if (!in_array($a->trigger_tag, $tags, true)) continue; }
                if ($a->action === 'enroll') self::enroll($uid, (int) $a->action_value, $contactIds, 'automation:' . $a->id);
                elseif ($a->action === 'stop') DB::table('ec_seq_enrollments')->whereIn('contact_id', $contactIds)->where('sequence_id', (int) $a->action_value)->where('status', 'active')->update(['status' => 'stopped', 'updated_at' => now()]);
                elseif (in_array($a->action, ['add_tag', 'remove_tag'], true) && ($t = CE::normTags($a->action_value))) {
                    foreach (DB::table('ec_contacts')->where('user_id', $uid)->whereIn('id', $contactIds)->get(['id', 'tags']) as $c) {
                        $cur = array_filter(explode(',', (string) $c->tags));
                        $new = $a->action === 'add_tag' ? array_unique(array_merge($cur, [$t])) : array_diff($cur, [$t]);
                        DB::table('ec_contacts')->where('id', $c->id)->update(['tags' => CE::normTags($new), 'updated_at' => now()]);
                    }
                    if ($a->action === 'add_tag') self::fire($uid, 'tag_added', $contactIds, $t, $depth + 1);
                }
                DB::table('ec_automations')->where('id', $a->id)->update(['runs' => DB::raw('runs + ' . count($contactIds)), 'last_run_at' => now()]);
            }
        } catch (\Throwable $e) { Log::warning('ec automation failed', ['uid' => $uid, 'e' => mb_substr($e->getMessage(), 0, 150)]); }
    }

    public static function sentToday(int $uid): int
    {
        return DB::table('ec_seq_sends')->where('user_id', $uid)->whereIn('status', ['sent', 'sending', 'failed'])->where('updated_at', '>=', now(CE::TZ)->startOfDay()->utc())->count();
    }

    /** Worker: due steps for ACTIVE sequences. Respects mailbox, block switch, shared daily limit, batch + spacing. */
    public static function run(): array
    {
        $out = ['sent' => 0, 'failed' => 0, 'skipped' => 0, 'waiting_mailbox' => 0];
        if (!config('ec.sending')) return $out + ['disabled' => true];
        $demo = (int) config('gvd_demo.user_id', 0);
        foreach (DB::table('ec_sequences')->where('status', 'ACTIVE')->get() as $seq) {
            $uid = (int) $seq->user_id;
            if ($uid === $demo || CE::blocked($uid)) continue;
            $smtp = CE::smtp($uid); if (!$smtp) { $out['waiting_mailbox']++; continue; }
            $room = min((int) config('ec.batch', 50), CE::limitFor($uid) - CE::sentToday($uid) - self::sentToday($uid));
            if ($room <= 0) continue;
            $steps = DB::table('ec_seq_steps')->where('sequence_id', $seq->id)->orderBy('position')->get();
            if ($steps->isEmpty()) continue;
            $snd = CE::sender($uid); $tr = null; $first = true;
            foreach (DB::table('ec_seq_enrollments')->where('sequence_id', $seq->id)->where('status', 'active')->orderBy('id')->limit(1000)->get() as $en) {
                if ($room <= 0) break;
                $done = DB::table('ec_seq_sends')->where('enrollment_id', $en->id)->pluck('step_id')->all();
                $step = $steps->first(fn ($s) => !in_array($s->id, $done));
                if (!$step) { DB::table('ec_seq_enrollments')->where('id', $en->id)->update(['status' => 'completed', 'updated_at' => now()]); continue; }
                if (\Carbon\Carbon::parse($en->enrolled_at)->addDays($step->day_offset)->isFuture()) continue;
                $ct = DB::table('ec_contacts')->where('id', $en->contact_id)->where('user_id', $uid)->first();
                if (!$ct || $ct->status !== 'active' || CE::isSuppressed($uid, $en->email)) { DB::table('ec_seq_enrollments')->where('id', $en->id)->update(['status' => 'stopped', 'updated_at' => now()]); $out['skipped']++; continue; }
                if (!DB::table('ec_seq_sends')->insertOrIgnore(['enrollment_id' => $en->id, 'step_id' => $step->id, 'user_id' => $uid, 'email' => $en->email, 'status' => 'sending', 'created_at' => now(), 'updated_at' => now()])) continue;
                try {
                    $tr = $tr ?: CE::transport($smtp);
                    if (!$first) sleep((int) config('ec.spacing_seconds', 3)); $first = false;
                    [$s, $h, $u] = CE::render($step, $ct, $snd);
                    $h = Tracking::apply($h, "ec_seq", (int) DB::table("ec_seq_sends")->where("enrollment_id", $en->id)->where("step_id", $step->id)->value("id"), (int) $seq->id, $en->email, $uid); // trk
                    CE::deliver($snd, $en->email, $s, $h, $u, $smtp, $tr);
                    DB::table('ec_seq_sends')->where('enrollment_id', $en->id)->where('step_id', $step->id)->update(['status' => 'sent', 'sent_at' => now(), 'updated_at' => now()]); $out['sent']++;
                } catch (\Throwable $e) {
                    DB::table('ec_seq_sends')->where('enrollment_id', $en->id)->where('step_id', $step->id)->update(['status' => 'failed', 'reason' => mb_substr($e->getMessage(), 0, 250), 'updated_at' => now()]); $out['failed']++;
                }
                $room--;
            }
            // safety: pause a sequence whose recent sends mostly fail
            $rec = DB::table('ec_seq_sends as x')->join('ec_seq_enrollments as e', 'e.id', '=', 'x.enrollment_id')->where('e.sequence_id', $seq->id)->where('x.updated_at', '>=', now()->subDay())
                ->selectRaw("SUM(x.status='sent') s, SUM(x.status='failed') f")->first();
            if (($rec->s + $rec->f) >= 20 && $rec->f / ($rec->s + $rec->f) > config('ec.max_fail_rate', 0.1)) DB::table('ec_sequences')->where('id', $seq->id)->update(['status' => 'PAUSED', 'updated_at' => now()]);
        }
        return $out;
    }
}
