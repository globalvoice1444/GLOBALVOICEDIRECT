<?php
namespace App\Http\Controllers;

use App\Fax\FaxInbox;
use App\Portal\BillingSession;
use Illuminate\Support\Facades\DB;

/** Customer profile (VO style, 2026-11-01): read-only, values exactly as the billing API returns them for the signed-in customer. */
class VoProfileController extends Controller
{
    /** Fields shown, in order, when the API returns them (label => key). Nothing else is shown; secrets are never listed. */
    private const SECTIONS = [
        'Business' => ['Company' => 'companyname', 'Customer name' => 'name', 'First name' => 'firstname', 'Last name' => 'lastname'],
        'Contact' => ['Contact' => 'cont1', 'Email' => 'email', 'Phone' => 'phone1', 'Alternate phone' => 'phone2', 'Fax' => 'faxnum'],
        'Address' => ['Address line 1' => 'baddr1', 'Address line 2' => 'baddr2', 'City' => 'city', 'State' => 'state', 'ZIP code' => 'zip', 'Country' => 'country'],
        'Account' => ['Account ID' => 'i_customer', 'Currency' => 'iso_4217', 'Balance' => 'balance', 'Billing period' => 'billing_period', 'Customer since' => 'creation_date', 'Status' => 'bill_status'],
    ];

    public function index()
    {
        $uid = session()->get('user_id');
        $info = null; $error = null;
        if (\App\Portal\Demo::on()) $error = null; else // sample-data login only: no billing call
        try {
            $ic = BillingSession::storedCustomer();
            if ($ic !== null && !in_array($ic, FaxInbox::tenantsForUser($uid), true)) $error = 'Not allowed for this account.';
            elseif (!($k = BillingSession::get())) $error = 'Sign in again to see your account details.';
            else {
                $acct = in_array($k['realm'] ?? '', ['account', 'accounts'], true);
                $r = BillingSession::call($k, $acct ? 'Account/get_account_info' : 'Customer/get_customer_info');
                $info = $r[$acct ? 'account_info' : 'customer_info'] ?? null;
                if (!$info) $error = 'Your account details could not be loaded right now. Please try again.';
            }
        } catch (\Throwable $e) { $error = 'Your account details could not be loaded right now. Please try again.'; }

        $sections = [];
        if (\App\Portal\Demo::on()) $sections = \App\Portal\Demo::profileSections();
        else
        foreach (self::SECTIONS as $title => $fields) {
            foreach ($fields as $label => $key) {
                $v = is_array($info) ? ($info[$key] ?? null) : null;
                if ($v === null || $v === '' || is_array($v)) continue;
                $sections[$title][$label] = (string) $v;
            }
        }

        // STATIC credit line (2026-11-01, owner): same values the old profile page showed. Make dynamic later.
        $p = DB::table('payable_amount')->where('user_id', $uid)->first();
        $owed = $p ? round(floatval($p->r_amount), 3) : 0;
        $credit = ['static' => true, 'limit' => 7500, 'balance' => $owed, 'available' => 7500 - $owed, 'terms' => '50% down'];

        $fallbackName = (string) (DB::table('wpgc_users')->where('ID', $uid)->value('display_name') ?? '');
        return view('users.vo-profile', ['sections' => $sections, 'error' => $error, 'credit' => $credit, 'fallbackName' => $fallbackName]);
    }
}
