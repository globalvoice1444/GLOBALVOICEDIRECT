<?php

namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FaxController extends Controller
{
    /** POST /api/inbound/fax  (api group: no session, no CSRF). Resend email.received webhook. */
    public function inbound(Request $req)
    {
        $raw = $req->getContent();
        if (!FaxInbox::verifySvix($raw, $req->header('svix-id'), $req->header('svix-timestamp'), $req->header('svix-signature')))
            return response()->json(['error' => 'invalid signature'], 401);
        $evt = json_decode($raw, true);
        if (!is_array($evt)) return response()->json(['error' => 'bad json'], 400);
        if (($evt['type'] ?? '') !== 'email.received') return response()->json(['ignored' => true]);
        $emailId = (string) ($evt['data']['email_id'] ?? '');
        if ($emailId === '') return response()->json(['error' => 'no email id'], 400);
        try {
            return response()->json(FaxInbox::ingest(FaxInbox::fetchResendEmail($emailId)));
        } catch (\Throwable $e) {
            Log::error('gvd-fax ingest failed', ['email_id' => $emailId, 'error' => get_class($e)]);
            return response()->json(['error' => 'ingest failed'], 500); // Resend retries
        }
    }

    /** GET /user/fax  (customer portal; tenant from session only) */
    public function index(Request $req)
    {
        $tenants = FaxInbox::tenantsForUser(session()->get('user_id'));
        $faxes = $tenants ? DB::table('gvd_faxes')->where('status', 'stored')->whereIn('i_customer', $tenants)
            ->orderByDesc('received_at')->limit(500)->get() : collect();
        $faxes->each(function ($f) { $s = FaxInbox::sign((int) $f->i_customer, $f->uuid); $f->view_url = url("/user/fax/{$f->uuid}/file?exp={$s['exp']}&sig={$s['sig']}"); });
        $audit = $tenants ? DB::table('gvd_fax_audit')->whereIn('i_customer', $tenants)->orderByDesc('id')->limit(50)->get() : collect();
        return view('users.fax', ['faxes' => $faxes, 'audit' => $audit, 'retentionDays' => (int) config('fax.retention_days', 90)]);
    }

    /** GET /user/fax/{uuid}/file?exp&sig  (session + tenant + 5-minute signature) */
    public function file(Request $req, string $uuid)
    {
        $tenants = FaxInbox::tenantsForUser(session()->get('user_id'));
        $f = DB::table('gvd_faxes')->where('uuid', $uuid)->where('status', 'stored')->whereIn('i_customer', $tenants ?: [-1])->first();
        if (!$f) abort(404);
        if (!FaxInbox::checkSig((int) $f->i_customer, $uuid, (string) $req->query('exp', ''), (string) $req->query('sig', '')))
            return redirect('/user/fax')->with('error', 'That fax link expired. Please open it again.');
        $path = FaxInbox::path($f);
        if (!is_file($path)) abort(404);
        $download = $req->boolean('download');
        DB::table('gvd_fax_audit')->insert(['fax_uuid' => $uuid, 'i_customer' => $f->i_customer, 'user_id' => session()->get('user_id'),
            'action' => $download ? 'download' : 'view', 'ip' => $req->ip(), 'created_at' => now()]);
        $name = preg_replace('/[^\w.-]/', '', $f->filename);
        return response(FaxInbox::decrypt(file_get_contents($path)), 200, [
            'Content-Type' => 'application/pdf', 'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => ($download ? 'attachment' : 'inline') . "; filename=\"$name\"",
        ]);
    }
}
