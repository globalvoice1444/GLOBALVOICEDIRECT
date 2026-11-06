<?php

namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Portal\BillingSession;
use App\Support\AdminAlerts;
use App\Support\BusinessListings as BL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Customer Business Listings (free path: Bing queue + printable guide). Tenant-scoped. No email. */
class BusinessListingsController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache'];

    private function ctx(): array
    {
        $uid = (int) session()->get('user_id'); $ic = null;
        if (!$uid) abort(403);
        try { $k = BillingSession::get(); if ($k && in_array((int) $k['i_customer'], FaxInbox::tenantsForUser($uid), true)) $ic = (int) $k['i_customer']; } catch (\Throwable $e) {}
        return ['uid' => $uid, 'ic' => $ic];
    }
    private function mine(array $c) { return DB::table(BL::TABLE)->where('user_id', $c['uid'])->where(fn ($q) => $c['ic'] === null ? $q->whereNull('i_customer') : $q->where('i_customer', $c['ic'])); }

    public function index(Request $r)
    {
        $c = $this->ctx();
        $list = $this->mine($c)->orderByDesc('updated_at')->get();
        $edit = $r->query('edit') ? $list->firstWhere('id', (int) $r->query('edit')) : null;
        $done = session('bl_done') ? $list->firstWhere('id', (int) session('bl_done')) : null;
        return response()->view('users.gvd-business-listings', ['admin' => false, 'error' => null, 'list' => $list, 'edit' => $edit, 'done' => $done, 'days' => BL::DAYS])->withHeaders(self::NC);
    }

    public function save(Request $r)
    {
        $c = $this->ctx();
        $v = $r->validate([
            'business_name' => 'required|string|max:200', 'street' => 'required|string|max:200', 'suite' => 'nullable|string|max:100',
            'city' => 'required|string|max:100', 'state' => 'required|string|max:50', 'zip' => 'required|string|max:10', 'country' => 'nullable|string|max:2',
            'phone' => 'required|string|max:30', 'website' => 'nullable|string|max:255', 'email' => 'required|email|max:190',
            'category' => 'required|string|max:150', 'cat1' => 'nullable|string|max:150', 'cat2' => 'nullable|string|max:150', 'cat3' => 'nullable|string|max:150', 'cat4' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:750', 'contact_name' => 'required|string|max:150', 'consent' => 'accepted',
        ], ['consent.accepted' => 'Please confirm the consent checkbox.'], ['business_name' => 'business name', 'contact_name' => 'your name', 'category' => 'primary category']);
        $country = strtoupper(preg_replace('/[^A-Za-z]/', '', $v['country'] ?? 'US')) ?: 'US';
        $d = [
            'business_name' => trim($v['business_name']), 'street' => BL::normStreet($v['street']), 'suite' => BL::normStreet($v['suite'] ?? ''),
            'city' => BL::titleCase($v['city']), 'state' => strtoupper(trim($v['state'])), 'zip' => trim($v['zip']), 'country' => substr($country, 0, 2),
            'website' => trim((string) ($v['website'] ?? '')), 'email' => trim($v['email']), 'category' => trim($v['category']),
            'description' => mb_substr(trim((string) ($v['description'] ?? '')), 0, 750), 'contact_name' => trim($v['contact_name']),
        ];
        if ($d['website'] && !preg_match('#^https?://#i', $d['website'])) $d['website'] = 'https://' . $d['website'];
        $e = [];
        $phone = BL::e164($v['phone'], $d['country']);
        if (!$phone) $e['phone'] = 'Enter a valid main phone number.';
        if ($d['website'] && !filter_var($d['website'], FILTER_VALIDATE_URL)) $e['website'] = 'Enter a valid website address.';
        if ($d['country'] === 'US' && !preg_match('/^\d{5}(-\d{4})?$/', $d['zip'])) $e['zip'] = 'Enter a valid ZIP code.';
        if ($d['country'] === 'US' && !preg_match('/^[A-Z]{2}$/', $d['state'])) $e['state'] = 'Use the 2-letter state code (e.g. FL).';
        $extra = [];
        for ($i = 1; $i <= 4; $i++) if (trim((string) ($v["cat$i"] ?? '')) !== '') $extra[] = mb_substr(trim($v["cat$i"]), 0, 150);
        $hours = [];
        foreach (BL::DAYS as $k => $day) {
            $closed = (bool) $r->input("closed_$k");
            $o = preg_match('/^\d{2}:\d{2}$/', (string) $r->input("open_$k")) ? $r->input("open_$k") : '';
            $cl = preg_match('/^\d{2}:\d{2}$/', (string) $r->input("close_$k")) ? $r->input("close_$k") : '';
            if (!$closed && (!$o || !$cl)) $e["hours_$k"] = "Set open and close times for $day, or mark it closed.";
            $hours[$k] = ['closed' => $closed, 'open' => $closed ? '' : $o, 'close' => $closed ? '' : $cl];
        }
        $existing = $phone ? DB::table(BL::TABLE)->where('phone', $phone)->first(['id', 'user_id', 'i_customer']) : null;
        if ($existing && ((int) $existing->user_id !== $c['uid'] || ($existing->i_customer !== null && (int) $existing->i_customer !== $c['ic']))) {
            AdminAlerts::log('security', 'warning', 'Business listing phone already registered to another account', ['phone' => $phone, 'existing_listing_id' => (int) $existing->id], $c['uid'], $c['ic']);
            $e['phone'] = 'This phone number is already registered to another account. Please contact support.';
        }
        if ($e) return back()->withErrors($e)->withInput();

        $row = $d + ['phone' => $phone, 'extra_categories' => json_encode($extra), 'hours' => json_encode($hours), 'consent' => 1, 'ip' => (string) $r->ip(), 'status' => 'new', 'updated_at' => now()];
        if ($existing) { DB::table(BL::TABLE)->where('id', $existing->id)->update($row); $id = (int) $existing->id; $upd = true; }
        else { $id = DB::table(BL::TABLE)->insertGetId($row + ['user_id' => $c['uid'], 'i_customer' => $c['ic'], 'created_at' => now()]); $upd = false; }
        AdminAlerts::log('listings', 'info', ($upd ? 'Updated' : 'New') . ' business listing request: ' . $d['business_name'] . ' (' . $phone . ')', ['listing_id' => $id, 'phone' => $phone, 'updated' => $upd], $c['uid'], $c['ic']);
        return redirect('/user/business-listings')->with('bl_done', $id);
    }

    public function guide(int $id)
    {
        $c = $this->ctx();
        $r = $this->mine($c)->where('id', $id)->first();
        if (!$r) abort(404);
        return response()->view('users.gvd-listing-guide', ['r' => $r])->withHeaders(self::NC + ['X-Robots-Tag' => 'noindex, nofollow']);
    }
}
