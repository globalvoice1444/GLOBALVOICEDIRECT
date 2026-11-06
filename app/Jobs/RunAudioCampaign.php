<?php

namespace App\Jobs;

use App\Audio\PromptAudio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Places the calls of one audio campaign, one at a time, through the billing web callback. Never runs while sending is off. */
class RunAudioCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;
    public int $tries = 1;

    public function __construct(public int $campaignId) {}

    public function handle(): void
    {
        $c = DB::table('gvd_audio_campaigns')->where('id', $this->campaignId)->first();
        if (!$c) return;
        if (!config('audio_campaign.sending')) { Log::info('gvd-audio skipped: sending off', ['campaign' => $c->id]); return; }
        $ivr = (string) config('audio_campaign.ivr_number');
        // One generic recording for every number. play_prompt (call control) uses the recording's URL; otherwise the shared auto-attendant plays it.
        $promptUrl = PromptAudio::urlForRecording((int) $c->recording_id);
        $cc = config('audio_campaign.delivery') === 'callcontrol' && $promptUrl && PromptAudio::callControlAvailable();
        if (config('audio_campaign.delivery') === 'callcontrol' && !$cc) Log::info('gvd-audio call control unavailable, using auto-attendant', ['campaign' => $c->id]);
        if ((!$cc && $ivr === '') || !in_array($c->status, ['queued', 'running'], true) || !$c->caller_secret) return;
        try { $secret = Crypt::decryptString($c->caller_secret); } catch (\Throwable $e) { $this->finish($c->id, 'cancelled'); return; }

        DB::table('gvd_audio_campaigns')->where('id', $c->id)->update(['status' => 'running', 'started_at' => $c->started_at ?? now(), 'updated_at' => now()]);
        $url = (string) config('audio_campaign.callback_url');
        $gap = max(1, (int) config('audio_campaign.throttle_seconds'));

        while (true) {
            $now = DB::table('gvd_audio_campaigns')->where('id', $c->id)->value('status');
            if ($now !== 'running' || !config('audio_campaign.sending')) return; // paused / cancelled
            $t = DB::table('gvd_audio_campaign_targets')->where('campaign_id', $c->id)->where('status', 'waiting')->orderBy('id')->first();
            if (!$t) break;
            if (substr(preg_replace('/\D/', '', (string) $t->number), -10) === '5613635538') { // mcp9: protected fax-forwarded number, never dialed
                DB::table('gvd_audio_campaign_targets')->where('id', $t->id)->update(['status' => 'failed', 'result' => 'Not placed: protected number', 'attempted_at' => now(), 'updated_at' => now()]); continue;
            }
            DB::table('gvd_audio_campaign_targets')->where('id', $t->id)->update(['status' => 'calling', 'attempted_at' => now(), 'updated_at' => now()]);
            $st = 'failed'; $res = '';
            if ($cc) {
                $ok = PromptAudio::callAndPlay($c->caller_account, $t->number, $promptUrl);
                DB::table('gvd_audio_campaign_targets')->where('id', $t->id)->update(['status' => $ok ? 'placed' : 'failed', 'result' => $ok ? 'Call placed' : 'Not placed', 'updated_at' => now()]);
                sleep($gap); continue;
            }
            try {
                // server-to-server only; the URL (which carries the account password) is never logged or shown
                $r = Http::timeout(20)->connectTimeout(5)->withHeaders(['User-Agent' => 'Mozilla/5.0 (GVD Audio Campaign)', 'X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json, text/javascript, */*; q=0.01', 'Origin' => preg_replace('#^(https?://[^/]+).*#', '$1', $url), 'Referer' => preg_replace('#^(https?://[^/]+).*#', '$1', $url) . '/']) // mcp8: same headers as the platform's callback page
                    ->asForm()->post($url, ['Account' => $c->caller_account, 'Password' => $secret, 'First_Phone_Number' => $t->number, 'Second_Phone_Number' => $ivr, 'cb_type' => 'extended']); // mcp5: same as the platform's callback form
                // 2026-11-02 mcp2: the callback answers HTTP 200 even on failure; only success=1 in the JSON means placed.
                $j = $r->successful() ? json_decode((string) $r->body(), true) : null;
                if (is_array($j) && (int) ($j['success'] ?? 0) === 1) { $st = 'placed'; $res = 'Call placed'; }
                else { $st = 'failed'; $res = 'Not placed' . ($r->successful() ? (is_array($j) && !empty($j['error']) ? ': ' . mb_substr(strip_tags((string) $j['error']), 0, 180) : ' (unexpected reply)') : ' (HTTP ' . $r->status() . ')'); }
            } catch (\Throwable $e) { $res = 'Not placed (connection)'; }
            DB::table('gvd_audio_campaign_targets')->where('id', $t->id)->update(['status' => $st, 'result' => $res, 'updated_at' => now()]);
            sleep($gap);
        }
        $this->finish($c->id, 'done');
    }

    private function finish(int $id, string $status): void
    {
        DB::table('gvd_audio_campaigns')->where('id', $id)->update(['status' => $status, 'caller_secret' => null, 'finished_at' => now(), 'updated_at' => now()]);
    }

    public function failed(\Throwable $e): void
    {
        Log::warning('gvd-audio job failed', ['campaign' => $this->campaignId, 'error' => get_class($e)]);
        DB::table('gvd_audio_campaigns')->where('id', $this->campaignId)->update(['status' => 'paused', 'updated_at' => now()]);
    }
}
