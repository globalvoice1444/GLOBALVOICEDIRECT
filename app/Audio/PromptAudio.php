<?php

namespace App\Audio;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Audio Campaign generic recording (2026-11-01). The customer uploads one message; it is converted once, with a free
 * static ffmpeg, to 8 kHz mono mu-law .au (the format the billing platform's play_prompt accepts) and offered at an
 * unguessable per-recording URL. No paid services.
 */
class PromptAudio
{
    public static function convert(string $src, string $dst): bool
    {
        $bin = (string) config('audio_campaign.ffmpeg');
        if (!is_file($bin) || !is_executable($bin) || !is_file($src)) { Log::warning('gvd-audio convert unavailable'); return false; }
        $cmd = [$bin, '-hide_banner', '-loglevel', 'error', '-nostdin', '-y', '-i', $src, '-t', '300', '-vn', '-ac', '1', '-ar', '8000', '-c:a', 'pcm_mulaw', '-f', 'au', $dst];
        $p = proc_open($cmd, [0 => ['file', '/dev/null', 'r'], 1 => ['file', '/dev/null', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (!is_resource($p)) return false;
        stream_get_contents($pipes[2]); fclose($pipes[2]);
        $ok = proc_close($p) === 0 && is_file($dst) && filesize($dst) > 24 + 800; // at least ~0.1 s of audio
        if (!$ok) { @unlink($dst); Log::warning('gvd-audio convert failed'); }
        return $ok;
    }

    /** URL handed to play_prompt. The platform appends the extension itself, so none is included. */
    public static function urlForRecording(int $id): ?string
    {
        $tok = $id ? DB::table('gvd_audio_recordings')->where('id', $id)->value('play_token') : null;
        return $tok ? rtrim((string) config('audio_campaign.audio_base_url'), '/') . '/' . $tok : null;
    }

    /**
     * Call-control delivery (WebSocket CallControl: originate the call, then play_prompt with the URL).
     * Not available until the billing provider confirms call-control access and that play_prompt works on these calls.
     */
    public static function callControlAvailable(): bool
    {
        return false;
    }

    public static function callAndPlay(string $account, string $number, string $url): bool
    {
        Log::warning('gvd-audio call control requested but not available');
        return false;
    }
}
