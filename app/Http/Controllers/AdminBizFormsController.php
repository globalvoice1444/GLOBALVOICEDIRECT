<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/** Admin-only (adminauth group): marketing requests + credit applications. */
class AdminBizFormsController extends Controller
{
    const T = [
        'marketing' => ['table' => 'gvd_marketing_requests', 'title' => 'Marketing requests', 'statuses' => ['new', 'reviewed']],
        'credit' => ['table' => 'gvd_credit_applications', 'title' => 'Credit applications', 'statuses' => ['new', 'reviewed', 'approved', 'declined']],
    ];

    private function t(string $kind): array { if (!isset(self::T[$kind])) abort(404); return self::T[$kind] + ['kind' => $kind]; }

    const LEGACY_MASK = ['birthday' => 'Date of birth', 'driver_license' => "Driver's license", 'taxid' => 'Tax ID'];

    public static function mask($v): string
    {
        $v = trim((string) $v); if ($v === '') return '—';
        $tail = mb_substr(preg_replace('/[^A-Za-z0-9]/', '', $v), -4);
        return '•••• ' . $tail;
    }

    /** Credit list = new applications + legacy creditapp_info rows (read-only). */
    private function creditIndex(Request $r, array $t)
    {
        $st = (string) $r->query('status', ''); $src = (string) $r->query('source', '');
        $new = DB::table('gvd_credit_applications as f')->leftJoin('wpgc_users as u', 'u.ID', '=', 'f.user_id')
            ->selectRaw("'new' as source, f.id, f.created_at, f.user_id, u.display_name, f.legal_name, f.requested_amount, f.status, null as monthly_volume");
        if ($st !== '') $new->where('f.status', $st);
        $old = DB::table('creditapp_info as l')->leftJoin('wpgc_users as u', 'u.ID', '=', 'l.user_id')
            ->selectRaw("'legacy' as source, l.id, l.created_at, l.user_id, u.display_name, l.buisness_name as legal_name, null as requested_amount, 'legacy' as status, l.monthly_purchase_volume as monthly_volume");
        if ($src === 'new') $q = $new; elseif ($src === 'legacy') $q = $old; else $q = $st !== '' ? $new : $new->unionAll($old);
        $rows = DB::query()->fromSub($q, 'x')->orderByDesc('created_at')->orderByDesc('id')->paginate(50)->withQueryString();
        $counts = ['new' => DB::table('gvd_credit_applications')->count(), 'legacy' => DB::table('creditapp_info')->count()];
        return view('admin.gvd-bizforms', ['t' => $t, 'rows' => $rows, 'st' => $st, 'src' => $src, 'counts' => $counts, 'row' => null]);
    }

    public function legacyShow(Request $r, int $id, bool $reveal = false)
    {
        $t = $this->t('credit');
        $row = DB::table('creditapp_info as l')->leftJoin('wpgc_users as u', 'u.ID', '=', 'l.user_id')->select('l.*', 'u.display_name', 'u.user_email')->where('l.id', $id)->first();
        if (!$row) abort(404);
        $file = null;
        if (!empty($row->file_path)) { $p = ltrim((string) $row->file_path, '/'); if (is_file(public_path($p))) $file = ['url' => url('/admin/forms/credit/legacy/' . $row->id . '/file/upload'), 'name' => basename($p), 'image' => (bool) preg_match('/\.(png|jpe?g|gif|webp)$/i', $p)]; else $file = ['url' => null, 'name' => basename($p), 'image' => false]; }
        return view('admin.gvd-bizforms', ['t' => $t, 'rows' => null, 'st' => '', 'row' => null, 'legacy' => $row, 'reveal' => $reveal, 'file' => $file]);
    }

    /** Admin-only stream of a legacy upload (folder is web-denied). Files are only read, never changed. */
    public function legacyFile(int $id, string $field)
    {
        if ($field !== 'upload') abort(404);
        $p = DB::table('creditapp_info')->where('id', $id)->value('file_path');
        if (!$p) abort(404);
        $base = realpath(public_path('creditAppImages')); $full = realpath(public_path(ltrim((string) $p, '/')));
        if (!$base || !$full || !str_starts_with($full, $base . DIRECTORY_SEPARATOR) || !is_file($full)) abort(404);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($full) ?: 'application/octet-stream';
        $inline = in_array($mime, ['application/pdf', 'image/png', 'image/jpeg', 'image/gif', 'image/webp'], true);
        \App\Support\AdminAlerts::log('credit-file', 'info', 'Admin opened legacy credit application file', [
            'creditapp_info_id' => $id, 'file' => basename($full), 'admin_id' => session()->get('admin_id'), 'admin_name' => session()->get('admin_name')], null, null);
        return response()->file($full, ['Content-Type' => $mime, 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store',
            'Content-Disposition' => ($inline ? 'inline' : 'attachment') . '; filename="' . preg_replace('/[^A-Za-z0-9._-]/', '_', basename($full)) . '"']);
    }

    public function legacyReveal(Request $r, int $id)
    {
        if (!DB::table('creditapp_info')->where('id', $id)->exists()) abort(404);
        \App\Support\AdminAlerts::log('security', 'security', 'Admin revealed sensitive fields on legacy credit application', [
            'creditapp_info_id' => $id, 'fields' => array_keys(self::LEGACY_MASK), 'admin_id' => session()->get('admin_id'), 'admin_name' => session()->get('admin_name')], null, null);
        return $this->legacyShow($r, $id, true);
    }

    public function index(Request $r, string $kind)
    {
        $t = $this->t($kind); $st = (string) $r->query('status', '');
        if ($kind === 'credit') return $this->creditIndex($r, $t);
        $q = DB::table($t['table'] . ' as f')->leftJoin('wpgc_users as u', 'u.ID', '=', 'f.user_id')->select('f.*', 'u.display_name');
        if ($st !== '') $q->where('f.status', $st);
        $rows = $q->orderByDesc('f.id')->paginate(50)->withQueryString();
        return view('admin.gvd-bizforms', ['t' => $t, 'rows' => $rows, 'st' => $st, 'row' => null]);
    }

    public function show(string $kind, int $id)
    {
        $t = $this->t($kind);
        $row = DB::table($t['table'] . ' as f')->leftJoin('wpgc_users as u', 'u.ID', '=', 'f.user_id')->select('f.*', 'u.display_name', 'u.user_email')->where('f.id', $id)->first();
        if (!$row) abort(404);
        $ein = null;
        if ($kind === 'credit' && $row->ein_encrypted) { try { $ein = Crypt::decryptString($row->ein_encrypted); $ein = substr($ein, 0, 2) . '-' . substr($ein, 2); } catch (\Throwable $e) { $ein = '(unreadable)'; } }
        return view('admin.gvd-bizforms', ['t' => $t, 'rows' => null, 'st' => '', 'row' => $row, 'ein' => $ein]);
    }

    public function status(Request $r, string $kind, int $id)
    {
        $t = $this->t($kind);
        $s = (string) $r->input('status'); if (!in_array($s, $t['statuses'], true)) abort(422);
        DB::table($t['table'])->where('id', $id)->update(['status' => $s, 'updated_at' => now()]);
        return redirect("/admin/forms/$kind/$id")->with('message', 'Status updated to ' . $s . '.');
    }
}
