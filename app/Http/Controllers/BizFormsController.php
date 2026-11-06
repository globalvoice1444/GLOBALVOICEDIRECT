<?php

namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Portal\BillingSession;
use App\Support\AdminAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/** Customer Marketing form + Credit application. Tenant-scoped (user_id + i_customer). No email. */
class BizFormsController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache'];
    const GOALS = ['calls' => 'More phone calls', 'website' => 'New or better website', 'seo' => 'SEO / search ranking', 'social' => 'Social media', 'gbp' => 'Google Business Profile', 'reviews' => 'More online reviews'];
    const BUDGETS = ['under_500' => 'Under $500 / month', '500_1000' => '$500 – $1,000 / month', '1000_2500' => '$1,000 – $2,500 / month', '2500_plus' => '$2,500+ / month', 'unsure' => 'Not sure yet'];
    const TIMES = ['Morning (9 AM – 12 PM)', 'Afternoon (12 – 5 PM)', 'Evening (5 – 7 PM)', 'Any time'];
    const ENTITIES = ['LLC', 'Corporation', 'S-Corp', 'Partnership', 'Sole proprietorship', 'Nonprofit', 'Other'];

    private function ctx(): array
    {
        $uid = (int) session()->get('user_id'); $ic = null;
        try { $k = BillingSession::get(); if ($k && in_array((int) $k['i_customer'], FaxInbox::tenantsForUser($uid), true)) $ic = (int) $k['i_customer']; } catch (\Throwable $e) {}
        if (!$uid) abort(403);
        return ['uid' => $uid, 'ic' => $ic];
    }

    private function mine(string $table, array $c)
    {
        return DB::table($table)->where('user_id', $c['uid'])->where(fn ($q) => $c['ic'] === null ? $q->whereNull('i_customer') : $q->where('i_customer', $c['ic']))->orderByDesc('id');
    }

    private function user(int $uid) { try { return DB::table('wpgc_users')->where('ID', $uid)->first(['display_name', 'user_email']); } catch (\Throwable $e) { return null; } }

    // ---------- Marketing ----------
    public function marketing()
    {
        $c = $this->ctx(); $u = $this->user($c['uid']);
        $past = $this->mine('gvd_marketing_requests', $c)->limit(50)->get(['id', 'business_name', 'goals', 'status', 'created_at']);
        return response()->view('users.gvd-marketing-form', ['admin' => false, 'error' => null, 'past' => $past, 'u' => $u, 'goals' => self::GOALS, 'budgets' => self::BUDGETS, 'times' => self::TIMES,
            'industries' => array_values(array_unique(array_map(fn ($s) => $s[3] ?? 'General', MessagingController::library())))])->withHeaders(self::NC);
    }

    public function marketingSave(Request $r)
    {
        $c = $this->ctx();
        $v = $r->validate([
            'contact_name' => 'required|string|max:120', 'business_name' => 'required|string|max:160', 'email' => 'required|email|max:160',
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+().\-\s]{7,}$/'], 'website' => 'nullable|string|max:200', 'industry' => 'nullable|string|max:80',
            'goals' => 'nullable|array', 'goals.*' => 'in:' . implode(',', array_keys(self::GOALS)), 'target_area' => 'nullable|string|max:200',
            'budget_range' => 'nullable|in:' . implode(',', array_keys(self::BUDGETS)), 'contact_time' => 'nullable|string|max:60', 'message' => 'nullable|string|max:4000',
        ]);
        $id = DB::table('gvd_marketing_requests')->insertGetId($v = array_merge($v, [
            'user_id' => $c['uid'], 'i_customer' => $c['ic'], 'goals' => json_encode(array_values($v['goals'] ?? [])), 'status' => 'new', 'created_at' => now(), 'updated_at' => now()]));
        AdminAlerts::log('marketing', 'info', 'New marketing form request', ['request_id' => $id, 'business' => $v['business_name'], 'contact' => $v['contact_name']], $c['uid'], $c['ic']);
        return redirect('/user/marketing-form')->with('bf_ok', "Thank you! Your marketing request #$id was received. Our team will contact you.");
    }

    // ---------- Credit application ----------
    public function credit()
    {
        $c = $this->ctx(); $u = $this->user($c['uid']);
        $past = $this->mine('gvd_credit_applications', $c)->limit(50)->get(['id', 'legal_name', 'requested_amount', 'status', 'created_at']);
        $legacy = DB::table('creditapp_info')->where('user_id', $c['uid'])->orderByDesc('id')->limit(20)->get(['id', 'buisness_name', 'taxid', 'created_at']);
        return response()->view('users.gvd-credit-application', ['admin' => false, 'error' => null, 'past' => $past, 'legacy' => $legacy, 'u' => $u, 'entities' => self::ENTITIES])->withHeaders(self::NC);
    }

    public function creditSave(Request $r)
    {
        $c = $this->ctx();
        $ph = ['required', 'string', 'max:40', 'regex:/^[0-9+().\-\s]{7,}$/'];
        $v = $r->validate([
            'legal_name' => 'required|string|max:160', 'dba' => 'nullable|string|max:160', 'entity_type' => 'required|in:' . implode(',', self::ENTITIES),
            'years_in_business' => 'required|integer|min:0|max:300', 'ein' => ['nullable', 'regex:/^\d{2}-?\d{7}$/'],
            'address' => 'required|string|max:300', 'phone' => $ph, 'email' => 'required|email|max:160',
            'applicant_name' => 'required|string|max:120', 'applicant_title' => 'required|string|max:80',
            'requested_amount' => 'required|integer|min:1|max:10000000', 'monthly_usage' => 'required|integer|min:0|max:10000000',
            'ref1_name' => 'required|string|max:120', 'ref1_phone' => $ph, 'ref2_name' => 'required|string|max:120', 'ref2_phone' => $ph,
            'bank_name' => 'required|string|max:120', 'agreed' => 'accepted', 'signature' => 'required|string|max:120', 'signed_date' => 'required|date',
        ], ['ein.regex' => 'EIN must be 9 digits (format 12-3456789).'], ['ein' => 'EIN', 'dba' => 'DBA', 'ref1_name' => 'reference 1 name', 'ref1_phone' => 'reference 1 phone', 'ref2_name' => 'reference 2 name', 'ref2_phone' => 'reference 2 phone', 'agreed' => 'agreement']);
        $ein = trim((string) ($v['ein'] ?? '')); unset($v['ein']);
        $v['ein_encrypted'] = $ein !== '' ? Crypt::encryptString(preg_replace('/\D/', '', $ein)) : null;
        $v['agreed'] = 1;
        $id = DB::table('gvd_credit_applications')->insertGetId(array_merge($v, ['user_id' => $c['uid'], 'i_customer' => $c['ic'], 'status' => 'new', 'created_at' => now(), 'updated_at' => now()]));
        AdminAlerts::log('credit', 'info', 'New credit application', ['application_id' => $id, 'legal_name' => $v['legal_name'], 'requested_amount' => (int) $v['requested_amount']], $c['uid'], $c['ic']);
        return redirect('/user/credit-application')->with('bf_ok', "Thank you! Credit application #$id was submitted. We will review it and contact you.");
    }
}
