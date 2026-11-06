<?php

namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Portal\BillingSession;
use App\Portal\Greetings;
use App\Portal\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class GreetingsController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache'];

    /** Tenant-scoped service from the signed-in user's own billing session, or an error response. */
    private function svc(): Greetings|\Symfony\Component\HttpFoundation\Response
    {
        if (\App\Portal\Demo::on()) return \App\Portal\Demo::readonly(); // sample-data login: never touches the billing API (all writes go through here)
        $ic = BillingSession::storedCustomer();
        if ($ic !== null && !in_array($ic, FaxInbox::tenantsForUser(session()->get('user_id')), true))
            return response()->json(['error' => 'Not allowed for this account.'], 403, self::NC);
        $k = BillingSession::get();
        if (!$k) return response()->json(['error' => 'Your sign-in has expired. Please sign out and sign in again.', 'signin' => true], 401, self::NC);
        if (!in_array((int) $k['i_customer'], FaxInbox::tenantsForUser(session()->get('user_id')), true))
            return response()->json(['error' => 'Not allowed for this account.'], 403, self::NC);
        return new Greetings($k);
    }

    private function tz(): \DateTimeZone { return new \DateTimeZone('America/New_York'); }

    private function holidays(int $ic): array
    {
        return DB::table('gvd_holidays')->where('i_customer', $ic)->orderBy('day')->get(['id', 'name', 'day'])->map(fn ($h) => (array) $h)->all();
    }

    /** GET /user/greetings */
    public function index()
    {
        if (\App\Portal\Demo::on()) return response()->view('users.greetings', \App\Portal\Demo::greetings())->withHeaders(self::NC); // sample-data login only
        $g = $this->svc();
        if (!$g instanceof Greetings) {
            $err = json_decode($g->getContent(), true)['error'] ?? 'Not available';
            return response()->view('users.greetings', ['error' => $err, 'rows' => [], 'cards' => [], 'admin' => false, 'nowText' => null, 'holidays' => [], 'notes' => []], $g->getStatusCode() === 403 ? 403 : 200)->withHeaders(self::NC);
        }
        $rows = []; $notes = []; $nowText = null; $cards = [];
        $now = new \DateTimeImmutable('now', $this->tz());
        try {
            $exts = $g->extensions();
            try {
                $c = $g->company($now);
            } catch (\Throwable $e) {
                $c = null; $notes[] = 'Company greetings could not be loaded with your sign-in, so they are not shown.';
                Log::warning('gvd-greet company load failed', ['i_customer' => $g->ic, 'error' => get_class($e), 'm' => substr(preg_replace('/[^\w .:]/', '', $e->getMessage()), 0, 80)]);
            }
            if ($c && $c['root']) {
                $menus = $c['menus']; $root = $menus[$c['root']];
                $after = $c['after'] ? ($menus[$c['after']] ?? null) : null;
                $alts = array_values(array_filter($menus, fn ($m) => (int) $m['i_menu'] !== $c['root'] && (int) $m['i_menu'] !== $c['after']));
                $mk = fn ($m, $label, $kind, $altList) => [
                    'key' => "menu:{$m['i_menu']}", 'kind' => $kind, 'name' => $label, 'i_menu' => (int) $m['i_menu'],
                    'audio' => url("/user/greetings/audio?t=menu&id={$m['i_menu']}"), 'custom' => ($m['msg_menu_type'] ?? '') === 'custom',
                    'schedule' => Period::describe($m['period'] ?? '', $m['period_desc'] ?? null), 'period' => (string) ($m['period'] ?? ''),
                    'noInput' => $g->noInputText($m, $exts), 'alternates' => $altList, 'deletable' => false,
                ];
                $altRows = array_map(fn ($m) => ['key' => "menu:{$m['i_menu']}", 'name' => (string) $m['name'], 'i_menu' => (int) $m['i_menu'],
                    'audio' => url("/user/greetings/audio?t=menu&id={$m['i_menu']}"), 'schedule' => Period::describe($m['period'] ?? '', $m['period_desc'] ?? null),
                    'period' => (string) ($m['period'] ?? ''), 'deletable' => true, 'active' => Period::active($m['period'] ?? '', $now)], $alts);
                // "Manage greetings" cards (display only for hours / no-input / sync).
                $sync = !empty($c['aa']); // all company numbers are answered by the one shared company auto attendant
                $card = fn ($m, $title, $hours) => ['title' => $title, 'key' => "menu:{$m['i_menu']}", 'i_menu' => (int) $m['i_menu'],
                    'audio' => url("/user/greetings/audio?t=menu&id={$m['i_menu']}"), 'custom' => ($m['msg_menu_type'] ?? '') === 'custom',
                    'hours' => $hours, 'noInput' => $g->noInputDetail($m, $exts), 'sync' => $sync];
                $rootHours = Period::describe($root['period'] ?? '', $root['period_desc'] ?? null);
                $cards[] = $card($root, 'Main', ['label' => $rootHours, 'periods' => array_values(array_filter(array_map('trim', preg_split('/[,;]/', $rootHours) ?: [])))]);
                if ($after) $cards[] = $card($after, 'After Hours', null);
                $rows[] = $mk($root, 'Company main greeting', 'company', $altRows);
                if ($after) $rows[] = $mk($after, 'After-hours greeting', 'company', []);
                $playing = $c['now'] ? ($menus[$c['now']] ?? null) : null;
                $nowText = $playing ? ((int) $playing['i_menu'] === $c['root'] ? 'Main greeting' : ((int) $playing['i_menu'] === $c['after'] ? 'After-hours greeting' : (string) $playing['name']))
                    . ' (' . Period::describe($playing['period'] ?? '', $playing['period_desc'] ?? null) . ')' : 'No greeting is active right now';
            } elseif ($c !== null) {
                $notes[] = 'No company auto attendant is set up for this account.';
            }
            foreach ($exts as $e) {
                $label = "Ext {$e['ext']}" . ($e['name'] !== '' && $e['name'] !== 'New Extension' ? " - {$e['name']}" : '');
                $rows[] = ['key' => "vm:{$e['i_account']}:standard", 'kind' => 'extension', 'name' => $label, 'i_account' => $e['i_account'],
                    'audio' => url("/user/greetings/audio?t=vm&id={$e['i_account']}&p=standard"), 'schedule' => 'Always', 'noInput' => null, 'deletable' => false,
                    'alternates' => [['key' => "vm:{$e['i_account']}:extended", 'name' => 'Extended absence', 'audio' => url("/user/greetings/audio?t=vm&id={$e['i_account']}&p=extended"),
                        'schedule' => 'When extended absence is turned on', 'deletable' => false, 'active' => false, 'extended' => true]]];
            }
        } catch (\Throwable $e) {
            Log::warning('gvd-greet load failed', ['i_customer' => $g->ic, 'error' => get_class($e)]);
            $notes[] = 'Greetings are unavailable right now. Please try again.';
        } finally { $g->close(); }
        return response()->view('users.greetings', ['error' => null, 'rows' => $rows, 'cards' => $cards, 'admin' => $g->isAdmin(), 'nowText' => $nowText,
            'holidays' => $this->holidays($g->ic), 'notes' => $notes])->withHeaders(self::NC);
    }

    /** GET /user/greetings/audio?t=menu|vm&id=&p= : streamed through our server as WAV. */
    public function audio(Request $req)
    {
        if (\App\Portal\Demo::on()) { $w = \App\Portal\Demo::chimeWav(); return response($w, 200, self::NC + ['Content-Type' => 'audio/wav', 'Content-Length' => strlen($w), 'X-Content-Type-Options' => 'nosniff']); } // sample-data login only
        $g = $this->svc(); if (!$g instanceof Greetings) return response('', $g->getStatusCode(), self::NC);
        $id = (int) $req->query('id'); $t = (string) $req->query('t');
        try {
            if ($t === 'menu') {
                $aa = $g->aaAccount(); if (!$aa) abort(404);
                $raw = $g->menuPrompt($aa, $id, 'menu');
            } elseif ($t === 'vm') {
                $p = in_array($req->query('p'), ['standard', 'extended'], true) ? $req->query('p') : 'standard';
                if (!$g->vmAccountOk($id)) return response('', 403, self::NC);
                $raw = $g->vmGreeting($id, $p);
            } else return response('', 400, self::NC);
        } catch (\DomainException $e) { return response('', 403, self::NC); }
        catch (\Throwable $e) { return response('', 502, self::NC); }
        finally { $g->close(); }
        $wav = $raw ? Greetings::auToWav($raw) : null;
        if (!$wav) return response('', 204, self::NC + ['X-Greeting' => 'none']); // no custom recording: not an error
        return response($wav, 200, self::NC + ['Content-Type' => 'audio/wav', 'Content-Length' => strlen($wav), 'X-Content-Type-Options' => 'nosniff']);
    }

    private function adminSvc(): Greetings|\Symfony\Component\HttpFoundation\Response
    {
        $g = $this->svc(); if (!$g instanceof Greetings) return $g;
        if (!$g->isAdmin()) return response()->json(['error' => 'Only the company administrator can change greetings.'], 403, self::NC);
        $key = 'greet-write:' . session()->get('user_id');
        if (RateLimiter::tooManyAttempts($key, 20)) return response()->json(['error' => 'Too many changes. Wait a minute.'], 429, self::NC);
        RateLimiter::hit($key, 60);
        return $g;
    }

    private function wavUpload(Request $req): ?string
    {
        $f = $req->file('audio');
        if (!$f || !$f->isValid() || $f->getSize() > config('greetings.max_upload_kb') * 1024) return null;
        $h = file_get_contents($f->getRealPath(), false, null, 0, 12);
        if (substr($h, 0, 4) !== 'RIFF' || substr($h, 8, 4) !== 'WAVE') return null;
        return $f->getRealPath();
    }

    private function ok(Greetings $g, string $what) { Log::info('gvd-greet change', ['i_customer' => $g->ic, 'user_id' => session()->get('user_id'), 'what' => $what]); return response()->json(['ok' => true], 200, self::NC); }
    private function fail(Greetings $g, \Throwable $e, string $what)
    {
        Log::warning('gvd-greet change failed', ['i_customer' => $g->ic, 'what' => $what, 'error' => get_class($e), 'm' => substr(preg_replace('/[^\w .:]/', '', $e->getMessage()), 0, 80)]);
        return response()->json(['error' => $e instanceof \DomainException ? 'Not allowed.' : 'The change could not be saved. Nothing was changed.'], $e instanceof \DomainException ? 403 : 502, self::NC);
    }

    /** POST /user/greetings/replace {target, audio(wav)} */
    public function replace(Request $req)
    {
        $g = $this->adminSvc(); if (!$g instanceof Greetings) return $g;
        $path = $this->wavUpload($req);
        if (!$path) return response()->json(['error' => 'Please record or choose a WAV/MP3 audio file (the page converts it for you).'], 422, self::NC);
        $t = (string) $req->input('target');
        try {
            if (preg_match('/^menu:(\d+)$/', $t, $m)) { $aa = $g->aaAccount(); if (!$aa) throw new \DomainException(); $g->setMenuPrompt($aa, (int) $m[1], $path); }
            elseif (preg_match('/^vm:(\d+):(standard|extended)$/', $t, $m)) { if (!$g->vmAccountOk((int) $m[1])) throw new \DomainException(); $g->setVmGreeting((int) $m[1], $path, $m[2]); }
            else return response()->json(['error' => 'Unknown greeting'], 422, self::NC);
            return $this->ok($g, "replace $t");
        } catch (\Throwable $e) { return $this->fail($g, $e, "replace $t"); } finally { $g->close(); }
    }

    /** POST /user/greetings/restore {target}: company menus or an extension's voicemail go back to the standard prompt. */
    public function restore(Request $req)
    {
        $g = $this->adminSvc(); if (!$g instanceof Greetings) return $g;
        $t = (string) $req->input('target');
        try {
            if (preg_match('/^vm:(\d+):standard$/', $t, $m)) {
                if (!$g->vmAccountOk((int) $m[1])) throw new \DomainException();
                $g->restoreVmDefault((int) $m[1]);
                return $this->ok($g, "restore $t");
            }
            if (!preg_match('/^menu:(\d+)$/', $t, $m)) return response()->json(['error' => 'Unknown greeting'], 422, self::NC);
            $aa = $g->aaAccount(); if (!$aa) throw new \DomainException();
            $g->updateMenu($aa, ['i_menu' => (int) $m[1], 'msg_menu_type' => 'standard']);
            return $this->ok($g, "restore $t");
        } catch (\Throwable $e) { return $this->fail($g, $e, "restore $t"); } finally { $g->close(); }
    }

    /** POST /user/greetings/delete {target}: removes an alternate company greeting (never the main or after-hours one). */
    public function delete(Request $req)
    {
        $g = $this->adminSvc(); if (!$g instanceof Greetings) return $g;
        $t = (string) $req->input('target');
        try {
            if (!preg_match('/^menu:(\d+)$/', $t, $m)) return response()->json(['error' => 'Only alternate company greetings can be deleted.'], 422, self::NC);
            $c = $g->company(new \DateTimeImmutable('now', $this->tz()));
            $id = (int) $m[1];
            if (!$c || !isset($c['menus'][$id]) || $id === $c['root'] || $id === $c['after']) throw new \DomainException();
            foreach ($c['menus'] as $menu) foreach ($menu['transitions'] as $tr) if ((int) ($tr['target_i_menu'] ?? 0) === $id)
                return response()->json(['error' => 'This greeting is still used by your call menu, so it was not deleted.'], 409, self::NC);
            $g->delMenu($c['aa'], $id);
            return $this->ok($g, "delete $t");
        } catch (\Throwable $e) { return $this->fail($g, $e, "delete $t"); } finally { $g->close(); }
    }

    private function periodFrom(Request $req, int $ic): ?array
    {
        $kind = (string) $req->input('schedule');
        if ($kind === 'weekly') {
            $days = (array) $req->input('days', []);
            $from = (string) $req->input('from', '09:00'); $to = (string) $req->input('to', '17:00');
            if (!preg_match('/^\d{1,2}:\d{2}$/', $from) || !preg_match('/^\d{1,2}:\d{2}$/', $to) || !$days) return null;
            return [Period::weekly($days, $from, $to), implode(',', $days) . " $from-$to"];
        }
        if ($kind === 'dates') {
            $d = array_values(array_filter((array) $req->input('dates', []), fn ($x) => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $x)));
            return $d ? [Period::dates($d), 'Dates: ' . implode(', ', $d)] : null;
        }
        if ($kind === 'holidays') {
            $d = array_map(fn ($h) => $h['day'], $this->holidays($ic));
            return $d ? [Period::dates($d), 'Company holidays'] : null;
        }
        return null;
    }

    /** POST /user/greetings/alternate {name, schedule, ..., audio}: adds an alternate company greeting with its own schedule. */
    public function alternate(Request $req)
    {
        $g = $this->adminSvc(); if (!$g instanceof Greetings) return $g;
        $name = trim(preg_replace('/[^\w \'-]/u', '', (string) $req->input('name')));
        if ($name === '' || strlen($name) > 40 || strtoupper($name) === 'ROOT') return response()->json(['error' => 'Give the greeting a short name.'], 422, self::NC);
        $per = $this->periodFrom($req, $g->ic);
        if (!$per) return response()->json(['error' => 'Choose when this greeting should play.'], 422, self::NC);
        $path = $this->wavUpload($req);
        if (!$path) return response()->json(['error' => 'Please record or choose an audio file.'], 422, self::NC);
        try {
            $aa = $g->aaAccount(); if (!$aa) throw new \DomainException();
            $id = $g->createMenu($aa, ['name' => $name, 'period' => $per[0], 'period_desc' => mb_substr($per[1], 0, 60)]);
            if ($id <= 0) throw new \RuntimeException('menu not created');
            $g->setMenuPrompt($aa, $id, $path);
            return $this->ok($g, "alternate add $id");
        } catch (\Throwable $e) { return $this->fail($g, $e, 'alternate add'); } finally { $g->close(); }
    }

    /** POST /user/greetings/schedule {target, schedule, ...}: changes when a company greeting plays. */
    public function schedule(Request $req)
    {
        $g = $this->adminSvc(); if (!$g instanceof Greetings) return $g;
        $t = (string) $req->input('target');
        if (!preg_match('/^menu:(\d+)$/', $t, $m)) return response()->json(['error' => 'Only company greetings have schedules.'], 422, self::NC);
        $per = $this->periodFrom($req, $g->ic);
        if (!$per) return response()->json(['error' => 'Choose when this greeting should play.'], 422, self::NC);
        try {
            $aa = $g->aaAccount(); if (!$aa) throw new \DomainException();
            $g->updateMenu($aa, ['i_menu' => (int) $m[1], 'period' => $per[0], 'period_desc' => mb_substr($per[1], 0, 60)]);
            return $this->ok($g, "schedule $t");
        } catch (\Throwable $e) { return $this->fail($g, $e, "schedule $t"); } finally { $g->close(); }
    }

    /** POST /user/greetings/holidays {action: add|remove, name, day | id}: the company's reusable holiday list (stored here). */
    public function holidaysSave(Request $req)
    {
        $g = $this->adminSvc(); if (!$g instanceof Greetings) return $g;
        if ($req->input('action') === 'remove') {
            DB::table('gvd_holidays')->where('i_customer', $g->ic)->where('id', (int) $req->input('id'))->delete();
        } else {
            $day = (string) $req->input('day'); $name = trim(mb_substr(strip_tags((string) $req->input('name')), 0, 100));
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day) || $name === '') return response()->json(['error' => 'Enter a name and a date.'], 422, self::NC);
            DB::table('gvd_holidays')->updateOrInsert(['i_customer' => $g->ic, 'day' => $day], ['name' => $name, 'updated_at' => now(), 'created_at' => now()]);
        }
        return response()->json(['ok' => true, 'holidays' => $this->holidays($g->ic)], 200, self::NC);
    }
}
