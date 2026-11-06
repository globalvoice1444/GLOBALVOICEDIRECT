<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

/** Streams script-recording audio from public/user-audios (direct web access is denied by .htaccess). Read-only. */
class ScriptAudioController extends Controller
{
    /** GET /user/phone-scripts/audio/{id} — only the customer who owns the script_info row. */
    public function user(int $id)
    {
        $uid = (int) session()->get('user_id');
        $r = DB::table('script_info')->where('id', $id)->first(['user_id', 'audio_file_path']);
        if (!$uid || !$r || (int) $r->user_id !== $uid) abort(403);
        return $this->stream($r->audio_file_path);
    }

    /** GET /admin/script-audio/{id} — admins (adminauth group). */
    public function admin(int $id)
    {
        return $this->stream(DB::table('script_info')->where('id', $id)->value('audio_file_path'));
    }

    private function stream($p)
    {
        if (!$p) abort(404);
        $base = realpath(public_path('user-audios')); $full = realpath(public_path(ltrim((string) $p, '/')));
        if (!$base || !$full || !str_starts_with($full, $base . DIRECTORY_SEPARATOR) || !is_file($full)) abort(404);
        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        $mime = $ext === 'ogg' ? 'audio/ogg' : ($ext === 'mp3' ? 'audio/mpeg' : ((new \finfo(FILEINFO_MIME_TYPE))->file($full) ?: 'application/octet-stream'));
        return response()->file($full, ['Content-Type' => $mime, 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store',
            'Content-Disposition' => 'inline; filename="' . preg_replace('/[^A-Za-z0-9._-]/', '_', basename($full)) . '"']);
    }
}