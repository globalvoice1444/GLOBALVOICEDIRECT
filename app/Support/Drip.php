<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/** 2026-11-02 Drip email engine (enroll, eligibility, render, send). */
class Drip
{
    const TYPES = ['signup' => 'Signup drip', 'winback' => 'Win-back', 'dunning' => 'Payment / dunning', 'reengage' => 'Re-engagement', 'motivation' => 'Motivation (workflow-enrolled)'];
    /** Conditions the engine can evaluate. Anything else blocks the step (logged as skipped). */
    const CONDITIONS = ['always' => 'Always send', 'not_unsubscribed' => 'Not unsubscribed (always enforced)', 'account_active' => 'Account still verified (user_status = 1)'];

    public static function suppressed(string $email): bool
    {
        return DB::table('email_suppressions')->where('email', strtolower(trim($email)))->exists();
    }

    public static function unsubscribeUrl(string $email): string
    {
        return URL::signedRoute('drip.unsubscribe', ['e' => strtolower(trim($email))]);
    }

    public static function vars(object $enr): array
    {
        $first = trim((string) ($enr->first_name ?? '')) ?: 'there';
        return ['{{first_name}}' => e($first), '{{email}}' => e($enr->email), '{{login_url}}' => config('drip.app_url') . '/login',
            '{{app_url}}' => config('drip.app_url'), '{{unsubscribe_url}}' => self::unsubscribeUrl($enr->email), '{{voice_note_url}}' => e((string) config('drip.voice_note_url')), '{{voice_note_block}}' => self::voiceBlock()];
    }

    /** Optional pre-recorded welcome voice note (DRIP_VOICE_NOTE_URL). Renders nothing until a recording is uploaded. */
    public static function voiceBlock(): string
    {
        $u = (string) config('drip.voice_note_url');
        if (!preg_match('#^https://#', $u)) return '';
        return '<p style="margin:22px 0 6px"><b>A personal welcome from our team</b></p><p><a href="' . e($u) . '" style="display:inline-block;background:#1E2A36;color:#fff;text-decoration:none;font-weight:600;padding:11px 20px;border-radius:10px">&#9654; Play the 60-second voice note</a></p>';
    }

    public static function render(object $step, object $enr): array
    {
        $v = self::vars($enr);
        $subject = html_entity_decode(strtr($step->subject, $v), ENT_QUOTES);
        $preview = e(html_entity_decode(strtr((string) $step->preview, $v), ENT_QUOTES));
        $body = strtr((string) $step->body_html, $v);
        $unsub = $v['{{unsubscribe_url}}'];
        $html = '<!doctype html><html><body style="margin:0;background:#F4F6F9;font-family:Inter,Segoe UI,Arial,sans-serif;color:#0F1B26">'
            . '<div style="display:none;max-height:0;overflow:hidden">' . $preview . '</div>'
            . '<table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F6F9;padding:24px 0"><tr><td align="center">'
            . '<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #E6EAF0">'
            . '<tr><td style="background:#1E2A36;padding:18px 28px;color:#fff;font-weight:700;font-size:18px">Global Voice Direct</td></tr>'
            . '<tr><td style="padding:28px;font-size:15px;line-height:1.6">' . $body . '</td></tr>'
            . '<tr><td style="padding:18px 28px;border-top:1px solid #E6EAF0;color:#64748B;font-size:12px;line-height:1.6">The Global Voice Direct Team<br>'
            . e(config('drip.address')) . ' &middot; ' . e(config('drip.phone')) . ' &middot; support@globalvoicedirect.com<br>'
            . 'You get these emails because you signed up at globalvoicedirect.com. <a href="' . e($unsub) . '" style="color:#64748B">Unsubscribe</a></td></tr>'
            . '</table></td></tr></table></body></html>';
        return [$subject, $html, $unsub];
    }

    /** 2026-11-05 Shared SMTP quota guard for drip, workflow and broadcast mailers. */
    public static function isQuotaError(\Throwable $e): bool
    {
        if ($e instanceof MailerQuotaException) return true;
        return (bool) preg_match('/quota|rate.?limit|too many (messages|emails|mails|connections|recipients)|sending limit|limit exceeded|try again later|\b(421|451|452)\b/i', $e->getMessage());
    }

    public static function pausedUntil(): ?\Carbon\Carbon
    {
        $u = \Illuminate\Support\Facades\Cache::get('mailer_pause_until');
        return ($u && now()->lt($u)) ? \Carbon\Carbon::parse($u) : null;
    }

    public static function pauseMailer(string $why): void
    {
        $until = now()->addMinutes(max(1, (int) config('drip.quota_pause_minutes', 60)));
        \Illuminate\Support\Facades\Cache::put('mailer_pause_until', $until->toIso8601String(), $until->copy()->addMinutes(5));
        Log::warning('mailer paused on SMTP quota', ['until' => $until->toIso8601String(), 'err' => mb_substr($why, 0, 200)]);
    }

    /** Successful sends in the last rolling hour (all mailers). */
    public static function sentLastHour(): int
    {
        $cut = now()->subHour()->timestamp;
        return count(array_filter((array) \Illuminate\Support\Facades\Cache::get('mailer_sent_ts', []), fn($t) => $t >= $cut));
    }

    /** Sends allowed this run: min(batch, hourly cap left), 0 while paused. */
    public static function sendBudget(): int
    {
        if (self::pausedUntil()) return 0;
        return max(0, min((int) config('drip.batch'), (int) config('drip.hourly_cap', 90) - self::sentLastHour()));
    }

    private static function noteSent(): void
    {
        $cut = now()->subHour()->timestamp;
        $ts = array_values(array_filter((array) \Illuminate\Support\Facades\Cache::get('mailer_sent_ts', []), fn($t) => $t >= $cut));
        $ts[] = now()->timestamp;
        \Illuminate\Support\Facades\Cache::put('mailer_sent_ts', $ts, 7200);
    }

    public static function deliver(string $to, string $subject, string $html, ?string $unsub = null): void
    {
        if ($p = self::pausedUntil()) throw new MailerQuotaException('mailer paused until ' . $p->toIso8601String());
        try { self::deliverRaw($to, $subject, $html, $unsub); }
        catch (\Throwable $e) {
            if (self::isQuotaError($e)) { self::pauseMailer($e->getMessage()); throw new MailerQuotaException($e->getMessage(), 0, $e); }
            throw $e;
        }
        self::noteSent();
    }

    private static function deliverRaw(string $to, string $subject, string $html, ?string $unsub = null): void
    {
        Mail::html($html, function ($m) use ($to, $subject, $unsub) {
            $m->to($to)->subject($subject)->replyTo(config('drip.reply_to'), 'Global Voice Direct');
            if ($unsub) {
                $h = $m->getSymfonyMessage()->getHeaders();
                $h->addTextHeader('List-Unsubscribe', '<' . $unsub . '>');
                $h->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            }
        });
    }

    /** Returns null when conditions pass, or a reason string. */
    public static function conditionsFail(object $step, object $enr): ?string
    {
        $c = json_decode((string) $step->conditions, true) ?: [];
        foreach ((array) ($c['all'] ?? []) as $rule) {
            if ($rule === 'always' || $rule === 'not_unsubscribed') continue;
            if ($rule === 'account_active') {
                if (!$enr->user_id || !DB::table('wpgc_users')->where('ID', $enr->user_id)->where('user_status', 1)->exists()) return 'condition:account_active';
                continue;
            }
            return 'unsupported_condition:' . substr((string) $rule, 0, 60);
        }
        return null;
    }

    /** Auto-enroll verified signups into ACTIVE signup sequences (only users registered after enroll_after). */
    public static function enrollSignups(): int
    {
        $n = 0;
        foreach (DB::table('drip_sequences')->where('type', 'signup')->where('trigger', 'verified_signup')->where('status', 'ACTIVE')->get() as $seq) {
            $since = $seq->enroll_after ?: now()->toDateTimeString();
            $users = DB::table('wpgc_users')->where('user_status', 1)->where('user_registered', '>=', $since)
                ->whereNotNull('user_email')->where('user_email', '<>', '')->limit(500)->get(['ID', 'user_email', 'display_name', 'user_nicename']);
            foreach ($users as $u) {
                $email = strtolower(trim($u->user_email));
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
                $n += DB::table('drip_enrollments')->insertOrIgnore(['sequence_id' => $seq->id, 'user_id' => $u->ID, 'email' => $email,
                    'first_name' => self::firstName($u->display_name ?: $u->user_nicename), 'source' => 'auto', 'status' => 'active',
                    'enrolled_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        return $n;
    }

    public static function firstName(?string $name): string
    {
        $name = trim((string) $name);
        if ($name === '' || str_contains($name, '@')) return '';
        return mb_substr(ucfirst(strtok($name, ' ')), 0, 60);
    }

    /** Process due steps. Returns counts. Sends nothing unless config('drip.enabled'). */
    public static function run(bool $dry = false): array
    {
        $out = ['enrolled' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0, 'capped' => 0, 'disabled' => !config('drip.enabled')];
        $active = DB::table('drip_sequences')->where('status', 'ACTIVE')->pluck('id');
        if ($active->isEmpty()) return $out;
        if (!$dry && config('drip.enabled')) $out['enrolled'] = self::enrollSignups();
        if (!config('drip.enabled') && !$dry) return $out;
        $budget = $dry ? max(1, config('drip.batch')) : self::sendBudget();   // 2026-11-05 hourly cap + quota pause
        if ($budget <= 0) return $out + ['paused_or_capped' => true];
        $capFrom = now()->subHours(config('drip.min_hours_between'));
        $enrs = DB::table('drip_enrollments')->whereIn('sequence_id', $active)->where('status', 'active')->orderBy('id')->limit(2000)->get();
        foreach ($enrs as $enr) {
            if ($budget <= 0) break;
            if (self::suppressed($enr->email)) { DB::table('drip_enrollments')->where('id', $enr->id)->update(['status' => 'stopped', 'updated_at' => now()]); continue; }
            $done = DB::table('drip_sends')->where('enrollment_id', $enr->id)->pluck('step_id')->all();
            $steps = DB::table('drip_steps')->where('sequence_id', $enr->sequence_id)->where('status', 'ACTIVE')->whereNotIn('id', $done ?: [0])->orderBy('position')->get();
            if ($steps->isEmpty()) {
                $any = DB::table('drip_steps')->where('sequence_id', $enr->sequence_id)->whereIn('status', ['DRAFT', 'PAUSED'])->exists();
                if (!$any) DB::table('drip_enrollments')->where('id', $enr->id)->update(['status' => 'completed', 'completed_at' => now(), 'updated_at' => now()]);
                continue;
            }
            $start = \Carbon\Carbon::parse($enr->enrolled_at);
            $step = null;
            foreach ($steps as $s) {
                $due = $start->copy()->addDays($s->day_offset);
                if ($due->isFuture()) continue;
                if ($due->lt(now()->subDays(config('drip.max_late_days')))) { self::log($enr, $s, 'skipped', 'too_late', $dry); $out['skipped']++; continue; }
                $step = $s; break;
            }
            if (!$step) continue;
            if (Workflow::recentlyEmailed($enr->email, $capFrom)) { $out['capped']++; continue; }
            if ($why = self::conditionsFail($step, $enr)) { self::log($enr, $step, 'skipped', $why, $dry); $out['skipped']++; continue; }
            if ($dry) { $out['sent']++; $budget--; continue; }
            // claim first (unique enrollment+step) so a crash or overlap can never double-send
            if (!DB::table('drip_sends')->insertOrIgnore(['enrollment_id' => $enr->id, 'step_id' => $step->id, 'sequence_id' => $enr->sequence_id, 'email' => $enr->email,
                'status' => 'sending', 'subject' => mb_substr($step->subject, 0, 255), 'created_at' => now(), 'updated_at' => now()])) continue;
            try {
                [$subj, $html, $unsub] = self::render($step, $enr);
                $html = Tracking::apply($html, "drip", (int) DB::table("drip_sends")->where("enrollment_id", $enr->id)->where("step_id", $step->id)->value("id"), (int) $step->id, $enr->email); // trk
                self::deliver($enr->email, $subj, $html, $unsub);
                DB::table('drip_sends')->where('enrollment_id', $enr->id)->where('step_id', $step->id)->update(['status' => 'sent', 'sent_at' => now(), 'updated_at' => now()]);
                $out['sent']++;
            } catch (\Throwable $e) {
                if (self::isQuotaError($e)) {   // 2026-11-05 not the recipient's fault: release the claim so it retries after the pause
                    DB::table('drip_sends')->where('enrollment_id', $enr->id)->where('step_id', $step->id)->where('status', 'sending')->delete();
                    $out['quota_paused'] = true; break;
                }
                DB::table('drip_sends')->where('enrollment_id', $enr->id)->where('step_id', $step->id)->update(['status' => 'failed', 'reason' => mb_substr($e->getMessage(), 0, 250), 'updated_at' => now()]);
                Log::warning('drip send failed', ['enrollment' => $enr->id, 'step' => $step->id, 'err' => mb_substr($e->getMessage(), 0, 200)]);
                $out['failed']++;
            }
            $budget--;
        }
        return $out;
    }

    private static function log(object $enr, object $step, string $status, string $reason, bool $dry): void
    {
        if ($dry) return;
        DB::table('drip_sends')->insertOrIgnore(['enrollment_id' => $enr->id, 'step_id' => $step->id, 'sequence_id' => $enr->sequence_id, 'email' => $enr->email,
            'status' => $status, 'reason' => $reason, 'subject' => mb_substr($step->subject, 0, 255), 'created_at' => now(), 'updated_at' => now()]);
    }
}

/** 2026-11-05 SMTP quota / rate-limit, or mailer paused. */
class MailerQuotaException extends \RuntimeException {}
