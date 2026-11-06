<?php

namespace App\Http\Controllers;

use App\Support\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** 2026-11-02 trk: public pixel + click redirect (no auth), and admin report page. */
class TrackingController extends Controller
{
    const GIF = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    public function open(Request $r, $tok)
    {
        try { if ($p = Tracking::parse((string) $tok)) Tracking::record($p[0], 'open', 0, (string) $r->ip(), (string) $r->userAgent()); } catch (\Throwable $e) {}
        return response(base64_decode(self::GIF), 200, ['Content-Type' => 'image/gif', 'Cache-Control' => 'no-store, no-cache, must-revalidate, private', 'Pragma' => 'no-cache']);
    }

    public function click(Request $r, $tok)
    {
        $p = Tracking::parse((string) $tok);
        $url = $p && $p[1] > 0 ? DB::table('email_track_links')->where('track_send_id', $p[0])->where('idx', $p[1])->value('url') : null;
        if (!$url || !preg_match('#^https?://#i', $url)) return redirect('https://globalvoicedirect.com');   // unknown/forged token: never redirect elsewhere
        try { Tracking::record($p[0], 'click', $p[1], (string) $r->ip(), (string) $r->userAgent()); } catch (\Throwable $e) {}
        return redirect()->away($url, 302)->header('Cache-Control', 'no-store')->header('X-Robots-Tag', 'noindex');
    }

    /** Customer: per-sequence opens/clicks (own sequences only). */
    public function seqReport(Request $r, $id)
    {
        $uid = (int) session()->get("user_id"); abort_unless($uid > 0, 403);
        $seq = DB::table("ec_sequences")->where("id", (int) $id)->where("user_id", $uid)->first();
        if (!$seq) return response()->json(["error" => "Sequence not found."], 404);
        $x = DB::table("ec_seq_sends as x")->join("ec_seq_enrollments as e", "e.id", "=", "x.enrollment_id")->where("e.sequence_id", $seq->id)->selectRaw("COUNT(*) total, SUM(x.status='sent') sent, SUM(x.status='failed') failed")->first();
        $st = ["total" => (int) $x->total, "sent" => (int) $x->sent, "failed" => (int) $x->failed] + (Tracking::summary("ec_seq", [$seq->id], $uid)[$seq->id] ?? ["tracked" => 0, "unique_opens" => 0, "opens" => 0, "unique_clicks" => 0, "clicks" => 0, "open_rate" => 0, "ctr" => 0]);
        $rows = Tracking::recipients("ec_seq", (int) $seq->id, 500, $uid);
        $subj = DB::table("ec_seq_sends as x")->join("ec_seq_steps as s", "s.id", "=", "x.step_id")->whereIn("x.id", $rows->pluck("send_id")->all() ?: [0])->pluck("s.subject", "x.id");
        foreach ($rows as $w) { $w->step = $subj[$w->send_id] ?? null; unset($w->id, $w->send_id); }
        return response()->json(["stats" => $st, "rows" => $rows, "links" => Tracking::linkStats("ec_seq", (int) $seq->id, $uid), "note" => "Opens are approximate: some email apps block images and others open them automatically. Clicks are more reliable."], 200, ["Cache-Control" => "no-store"]);
    }

    /** Admin > Automations > Email tracking. ?type=drip|broadcast|workflow &ref=ID &f=opened|clicked|unopened */
    public function admin(Request $r)
    {
        $type = in_array($r->query('type'), ['drip', 'broadcast', 'workflow'], true) ? $r->query('type') : 'broadcast';
        $names = [
            'broadcast' => DB::table('broadcasts')->orderByDesc('id')->pluck('name', 'id')->all(),
            'drip' => DB::table('drip_steps as st')->join('drip_sequences as q', 'q.id', '=', 'st.sequence_id')->orderBy('q.id')->orderBy('st.position')->selectRaw("st.id, CONCAT(q.name,' · Day ',st.day_offset,' · ',st.subject) n")->pluck('n', 'id')->all(),
            'workflow' => DB::table('workflows')->orderBy('id')->pluck('name', 'id')->all(),
        ][$type];
        $sum = Tracking::summary($type);
        $sent = [];
        if ($type === 'broadcast') $sent = DB::table('broadcast_recipients')->where('status', 'sent')->groupBy('broadcast_id')->selectRaw('broadcast_id k, COUNT(*) n')->pluck('n', 'k')->all();
        if ($type === 'drip') $sent = DB::table('drip_sends')->where('status', 'sent')->groupBy('step_id')->selectRaw('step_id k, COUNT(*) n')->pluck('n', 'k')->all();
        $ref = (int) $r->query('ref');
        $f = in_array($r->query('f'), ['opened', 'clicked', 'unopened'], true) ? $r->query('f') : null;
        return view('admin.tracking', ['type' => $type, 'names' => $names, 'sum' => $sum, 'sent' => $sent, 'ref' => $ref, 'f' => $f,
            'rows' => $ref ? Tracking::recipients($type, $ref, 1000, null, $f) : collect(), 'links' => $ref ? Tracking::linkStats($type, $ref) : collect(), 'types' => Tracking::TYPES]);
    }
}
