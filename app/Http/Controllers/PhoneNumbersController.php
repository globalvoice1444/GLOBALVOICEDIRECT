<?php

namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Portal\BillingSession;
use App\Portal\PhoneNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class PhoneNumbersController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache'];
    const EMAIL = ['none', 'forward', 'notify', 'copy', 'fwd_as_attachment'];
    const FAX = ['multi_png', 'multi_tiff', 'pdf', 'tiff'];
    const CLI = ['DEFAULT', 'VODIDNUMBER', 'CALLERNUMBER'];

    private function svc(): PhoneNumbers|\Symfony\Component\HttpFoundation\Response
    {
        if (\App\Portal\Demo::on()) return \App\Portal\Demo::readonly(); // sample-data login: never touches the billing API
        $ic = BillingSession::storedCustomer();
        $ten = FaxInbox::tenantsForUser(session()->get('user_id'));
        if ($ic !== null && !in_array($ic, $ten, true)) return response()->json(['error' => 'Not allowed for this account.'], 403, self::NC);
        $k = BillingSession::get();
        if (!$k) return response()->json(['error' => 'Your sign-in has expired. Please sign out and sign in again.', 'signin' => true], 401, self::NC);
        if (!in_array((int) $k['i_customer'], $ten, true)) return response()->json(['error' => 'Not allowed for this account.'], 403, self::NC);
        return new PhoneNumbers($k);
    }

    private function labels(int $ic): array
    {
        return DB::table('gvd_number_labels')->where('i_customer', $ic)->pluck('label', 'i_account')->map(fn ($v) => (string) $v)->all();
    }

    /** Account list for this customer, cached 5 minutes. */
    private function rows(PhoneNumbers $p, bool $fresh = false): array
    {
        $key = "gvd-numbers:rows:{$p->ic}";
        if ($fresh) Cache::forget($key);
        return Cache::remember($key, 300, fn () => $p->all());
    }

    public function index()
    {
        if (\App\Portal\Demo::on()) return response()->view('users.phone-numbers', ['error' => null, 'rows' => \App\Portal\Demo::numbers(), 'admin' => true, 'exts' => array_map(fn ($e) => ['ext' => $e['ext'], 'name' => $e['name']], array_slice(\App\Portal\Demo::extensions()['rows'], 0, 5))])->withHeaders(self::NC);
        $p = $this->svc();
        if (!$p instanceof PhoneNumbers) {
            $err = json_decode($p->getContent(), true)['error'] ?? 'Not available';
            return response()->view('users.phone-numbers', ['error' => $err, 'rows' => [], 'admin' => false, 'exts' => []], $p->getStatusCode() === 403 ? 403 : 200)->withHeaders(self::NC);
        }
        $rows = []; $exts = []; $error = null;
        try {
            $rows = $this->rows($p);
            $lab = $this->labels($p->ic);
            foreach ($rows as &$r) $r['label'] = $lab[$r['i_account']] ?? '';
            unset($r);
            try { foreach ($p->extensions() as $e) $exts[] = ['ext' => $e['ext'], 'name' => $e['name']]; } catch (\Throwable $e) {}
        } catch (\Throwable $e) {
            $error = 'Your phone numbers could not be loaded right now. Please try again.';
            Log::warning('gvd-numbers load failed', ['i_customer' => $p->ic, 'error' => get_class($e)]);
        }
        return response()->view('users.phone-numbers', ['error' => $error, 'rows' => $rows, 'admin' => $p->isAdmin(), 'exts' => $exts])->withHeaders(self::NC);
    }

    /** GET /user/phone-numbers/detail?ids=1,2,3 : active non-system lines only, fetched in parallel, cached 5 minutes. */
    public function detail(Request $req)
    {
        if (\App\Portal\Demo::on()) return response()->json(['details' => (object) \App\Portal\Demo::numberDetails()], 200, self::NC); // sample-data login only
        $p = $this->svc(); if (!$p instanceof PhoneNumbers) return $p;
        $want = array_slice(array_unique(array_map('intval', explode(',', (string) $req->query('ids', $req->query('id', ''))))), 0, 40);
        try {
            $byId = []; foreach ($this->rows($p) as $r) $byId[$r['i_account']] = $r;
            $out = []; $need = [];
            foreach ($want as $ia) {
                $r = $byId[$ia] ?? null;
                if (!$r || $r['kind'] === 'system' || $r['status'] !== 'Active') continue;
                $c = Cache::get("gvd-numbers:d:{$p->ic}:$ia");
                if (is_array($c)) $out[$ia] = $c; else $need[] = ['i_account' => $ia, 'vo' => $r['kind'] === 'vo', 'custom' => $r['custom']];
            }
            foreach ($p->details($need) as $ia => $d) { $out[$ia] = $d; Cache::put("gvd-numbers:d:{$p->ic}:$ia", $d, 300); }
            return response()->json(['details' => (object) $out], 200, self::NC);
        } catch (\Throwable $e) { return response()->json(['error' => 'Not available'], 502, self::NC); }
    }

    /** POST /user/phone-numbers/save {id, label?, route?, ext?, cli?, email?, fax?} — admin only. */
    public function save(Request $req)
    {
        $p = $this->svc(); if (!$p instanceof PhoneNumbers) return $p;
        if (!$p->isAdmin()) return response()->json(['error' => 'Only the company administrator can change phone numbers.'], 403, self::NC);
        $key = 'numbers-write:' . session()->get('user_id');
        if (RateLimiter::tooManyAttempts($key, 20)) return response()->json(['error' => 'Too many changes. Wait a minute.'], 429, self::NC);
        RateLimiter::hit($key, 60);
        $ia = (int) $req->input('id'); $done = [];
        try {
            $row = null; foreach ($this->rows($p) as $r) if ($r['i_account'] === $ia) $row = $r;
            if (!$row || $row['kind'] === 'system') return response()->json(['error' => 'Not found'], 404, self::NC);
            if ($req->has('label')) {
                $l = trim(mb_substr((string) $req->input('label'), 0, 60));
                DB::table('gvd_number_labels')->updateOrInsert(['i_customer' => $p->ic, 'i_account' => $ia], ['label' => $l, 'updated_at' => now(), 'created_at' => now()]);
                $done[] = 'label';
            }
            $route = (string) $req->input('route', '');
            if ($route !== '') {
                if ($row['kind'] !== 'vo' || !in_array($route, ['ROOT', 'TRANSFER'], true)) return response()->json(['error' => 'This line\'s call routing can\'t be changed here.'], 422, self::NC);
                $ext = (string) $req->input('ext', '');
                if ($route === 'TRANSFER' && !in_array($ext, array_column($p->extensions(), 'ext'), true)) return response()->json(['error' => 'Choose one of your extensions.'], 422, self::NC);
                if (PhoneNumbers::forwardingGuarded(['i_account' => $ia, 'id' => $row['id']])) return response()->json(['error' => 'This line\'s call routing can\'t be changed here.'], 422, self::NC);
                $p->setRoute($ia, $route, $ext); $done[] = 'route';
            }
            $cli = (string) $req->input('cli', '');
            if ($cli !== '') { if (!in_array($cli, self::CLI, true)) return response()->json(['error' => 'Unknown caller ID choice'], 422, self::NC); $p->setCli($ia, $cli); $done[] = 'cli'; }
            $email = $req->input('email'); $fax = $req->input('fax');
            if ($email !== null && !in_array($email, self::EMAIL, true)) $email = null;
            if ($fax !== null && !in_array($fax, self::FAX, true)) $fax = null;
            if ($email !== null || $fax !== null) { $p->setVm($ia, $email, $fax); $done[] = 'voicemail'; }
            Cache::forget("gvd-numbers:d:{$p->ic}:$ia"); if (in_array('cli', $done, true)) $this->rows($p, true);
            Log::info('gvd-numbers change', ['i_customer' => $p->ic, 'user_id' => session()->get('user_id'), 'i_account' => $ia, 'what' => $done]);
            return response()->json(['ok' => true], 200, self::NC);
        } catch (\Throwable $e) {
            Log::warning('gvd-numbers change failed', ['i_customer' => $p->ic, 'i_account' => $ia, 'done' => $done, 'error' => get_class($e), 'm' => substr(preg_replace('/[^\w .:]/', '', $e->getMessage()), 0, 80)]);
            $msg = $done ? 'Some changes were saved (' . implode(', ', $done) . ') but the rest could not be.' : 'The change could not be saved. Nothing was changed.';
            return response()->json(['error' => $e instanceof \DomainException ? 'This line\'s call routing can\'t be changed here.' : $msg], $e instanceof \DomainException ? 422 : 502, self::NC);
        } finally { $p->close(); }
    }
}
