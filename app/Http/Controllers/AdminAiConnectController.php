<?php

namespace App\Http\Controllers;

use App\Services\VoiceAccountProvisioner;
use App\Support\AiConnect;
use App\Support\AiOAuth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Admin AI Connect. Lists fingerprints and settings. Never selects key hashes or other secrets. */
class AdminAiConnectController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0'];
    const PAGE = 25;

    public function index(Request $r)
    {
        return response()->view('admin.ai-connect', [
            'keys' => $this->keys($r),
            'keyQ' => trim((string) $r->query('key_q', '')),
            'activity' => $this->activity($r),
            'actCustomer' => trim((string) $r->query('act_customer', '')),
            'actKey' => trim((string) $r->query('act_key', '')),
            'actAction' => trim((string) $r->query('act_action', '')),
            'actDate' => trim((string) $r->query('act_date', '')),
            'customers' => $this->customers($r),
            'custQ' => trim((string) $r->query('cust_q', '')),
            'providers' => $this->providerRows(),
        ])->withHeaders(self::NC);
    }

    public function revokeKey(Request $r)
    {
        $id = (int) $r->input('key_id');
        $key = DB::table('ai_keys')->where('id', $id)->where('prefix', '!=', AiOAuth::KEY_PREFIX)->first(['id', 'user_id', 'name', 'prefix', 'revoked_at']);
        if (!$key) return $this->back('error', 'That API key was not found.');
        if ($key->revoked_at !== null) return $this->back('message', 'That API key is already revoked.');
        DB::table('ai_keys')->where('id', $id)->update(['revoked_at' => now(), 'updated_at' => now()]);
        $this->audit((int) $key->user_id, $id, 'revoke', 'revoked key ' . $key->name . ' (' . $key->prefix . ')');
        return $this->back('message', 'API key revoked.');
    }

    public function toggleCustomer(Request $r)
    {
        $uid = (int) $r->input('user_id');
        if ($uid <= 0 || !DB::table('wpgc_users')->where('ID', $uid)->exists()) {
            return $this->back('error', 'That customer was not found.');
        }
        $on = filter_var($r->input('enabled'), FILTER_VALIDATE_BOOLEAN);
        AiConnect::setUserEnabled($uid, $on);
        $this->audit($uid, null, 'switch', $on ? 'turned AI Connect on' : 'turned AI Connect off');
        return $this->back('message', $on ? 'AI Connect turned on for that customer.' : 'AI Connect turned off for that customer.');
    }

    public function saveProvider(Request $r)
    {
        $id = (string) $r->input('provider', '');
        if (!array_key_exists($id, VoiceAccountProvisioner::providers())) {
            return $this->back('error', 'That provider is not in the registry.');
        }
        $on = filter_var($r->input('enabled'), FILTER_VALIDATE_BOOLEAN);
        $now = now();
        DB::table('ai_provider_overrides')->updateOrInsert(
            ['provider' => $id],
            ['enabled' => $on, 'updated_at' => $now, 'created_at' => $now]
        );
        $this->audit(0, null, 'provider', ($on ? 'showed provider ' : 'hid provider ') . $id);
        return $this->back('message', $on ? 'Provider is available to customers.' : 'Provider is hidden from customers.');
    }

    private function keys(Request $r): array
    {
        $q = trim((string) $r->query('key_q', ''));
        $query = DB::table('ai_keys as k')
            ->leftJoin('wpgc_users as u', 'u.ID', '=', 'k.user_id')
            ->where('k.prefix', '!=', AiOAuth::KEY_PREFIX)
            ->orderByDesc('k.id');
        if ($q !== '') {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $query->where(function ($w) use ($q, $like) {
                $w->where('u.user_login', 'like', $like)
                    ->orWhere('u.display_name', 'like', $like)
                    ->orWhere('u.user_nicename', 'like', $like)
                    ->orWhere('u.user_email', 'like', $like);
                if (ctype_digit($q)) $w->orWhere('k.user_id', (int) $q);
            });
        }
        $total = (clone $query)->count();
        $pages = $total === 0 ? 0 : (int) ceil($total / self::PAGE);
        $page = $this->page($r, 'keys_page', $pages);
        $rows = ($total === 0 ? collect() : (clone $query)->forPage($page, self::PAGE)->get([
            'k.id', 'k.user_id', 'k.name', 'k.prefix', 'k.scopes', 'k.daily_limit', 'k.last_used_at', 'k.revoked_at',
            'u.user_login', 'u.display_name', 'u.user_email',
        ]))->map(function ($k) {
            return [
                'id' => (int) $k->id,
                'customer' => $this->customerName($k),
                'user_id' => (int) $k->user_id,
                'name' => (string) $k->name,
                'prefix' => (string) $k->prefix,
                'scopes' => json_decode((string) $k->scopes, true) ?: [],
                'today' => AiConnect::usedToday((int) $k->id),
                'limit' => (int) $k->daily_limit,
                'last_used' => $k->last_used_at ? Carbon::parse($k->last_used_at)->tz('America/New_York')->format('M j, g:i A') . ' ET' : 'Never',
                'status' => $k->revoked_at ? 'Revoked' : 'Active',
            ];
        })->all();
        return ['rows' => $rows, 'page' => $page, 'pages' => $pages];
    }

    private function activity(Request $r): array
    {
        $customer = trim((string) $r->query('act_customer', ''));
        $key = trim((string) $r->query('act_key', ''));
        $action = trim((string) $r->query('act_action', ''));
        $date = trim((string) $r->query('act_date', ''));
        $query = DB::table('ai_activity as a')
            ->leftJoin('wpgc_users as u', 'u.ID', '=', 'a.user_id')
            ->leftJoin('ai_keys as k', 'k.id', '=', 'a.key_id')
            ->orderByDesc('a.id');
        if ($customer !== '') {
            $like = '%' . addcslashes($customer, '%_\\') . '%';
            $query->where(function ($w) use ($customer, $like) {
                $w->where('u.user_login', 'like', $like)->orWhere('u.display_name', 'like', $like)->orWhere('u.user_email', 'like', $like);
                if (ctype_digit($customer)) $w->orWhere('a.user_id', (int) $customer);
            });
        }
        if ($key !== '') {
            $like = '%' . addcslashes($key, '%_\\') . '%';
            $query->where(function ($w) use ($key, $like) {
                $w->where('k.name', 'like', $like)->orWhere('k.prefix', 'like', $like);
                if (ctype_digit($key)) $w->orWhere('a.key_id', (int) $key);
            });
        }
        if ($action !== '') {
            $query->where(function ($w) use ($action) {
                $w->where('a.tool', $action)->orWhere('a.method', $action);
            });
        }
        if (preg_match('/\A\d{4}-\d{2}-\d{2}\z/', $date)) $query->whereDate('a.created_at', $date);
        $total = (clone $query)->count();
        $pages = $total === 0 ? 0 : (int) ceil($total / self::PAGE);
        $page = $this->page($r, 'act_page', $pages);
        $rows = ($total === 0 ? collect() : (clone $query)->forPage($page, self::PAGE)->get([
            'a.id', 'a.user_id', 'a.method', 'a.tool', 'a.mode', 'a.status', 'a.summary', 'a.created_at',
            'u.user_login', 'u.display_name', 'k.name as key_name', 'k.prefix',
        ]))->map(function ($a) {
            return [
                'when' => $a->created_at ? Carbon::parse($a->created_at)->tz('America/New_York')->format('M j, g:i A') . ' ET' : '',
                'customer' => (int) $a->user_id > 0 ? $this->customerName($a) : 'System',
                'key' => $a->key_name ? $a->key_name . ' (' . $a->prefix . ')' : '—',
                'action' => $a->tool ?: $a->method,
                'status' => (string) $a->status,
                'summary' => (string) ($a->summary ?? ''),
            ];
        })->all();
        return ['rows' => $rows, 'page' => $page, 'pages' => $pages];
    }

    private function customers(Request $r): array
    {
        $q = trim((string) $r->query('cust_q', ''));
        $ids = collect();
        foreach (['ai_keys', 'ai_settings', 'ai_activity'] as $table) {
            if (Schema::hasTable($table)) $ids = $ids->merge(DB::table($table)->pluck('user_id'));
        }
        $ids = $ids->map(fn ($id) => (int) $id)->filter(fn ($id) => $id > 0)->unique()->values();
        $query = DB::table('wpgc_users as u')->leftJoin('ai_settings as s', 's.user_id', '=', 'u.ID')->orderBy('u.ID');
        if ($q !== '') {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $query->where(function ($w) use ($q, $like) {
                $w->where('u.user_login', 'like', $like)->orWhere('u.display_name', 'like', $like)->orWhere('u.user_email', 'like', $like);
                if (ctype_digit($q)) $w->orWhere('u.ID', (int) $q);
            });
        } else {
            $query->whereIn('u.ID', $ids->all() ?: [0]);
        }
        $total = (clone $query)->count();
        $pages = $total === 0 ? 0 : (int) ceil($total / self::PAGE);
        $page = $this->page($r, 'cust_page', $pages);
        $rows = ($total === 0 ? collect() : (clone $query)->forPage($page, self::PAGE)->get([
            'u.ID', 'u.user_login', 'u.display_name', 'u.user_email', 's.enabled',
        ]))->map(function ($u) {
            return [
                'user_id' => (int) $u->ID,
                'customer' => $this->customerName($u),
                'email' => (string) ($u->user_email ?? ''),
                'enabled' => $u->enabled === null ? true : (bool) $u->enabled,
            ];
        })->all();
        return ['rows' => $rows, 'page' => $page, 'pages' => $pages];
    }

    private function providerRows(): array
    {
        $overrides = [];
        if (Schema::hasTable('ai_provider_overrides')) {
            foreach (DB::table('ai_provider_overrides')->get(['provider', 'enabled']) as $row) {
                $overrides[(string) $row->provider] = (bool) $row->enabled;
            }
        }
        $rows = [];
        foreach (VoiceAccountProvisioner::providers() as $id => $p) {
            $rows[] = [
                'id' => $id,
                'label' => (string) $p['label'],
                'category' => (string) ($p['category'] ?? ''),
                'gateway_supported' => !empty($p['gateway_supported']),
                'enabled' => $overrides[$id] ?? true,
            ];
        }
        return $rows;
    }

    private function page(Request $r, string $name, int $pages): int
    {
        $page = max(1, (int) $r->query($name, 1));
        if ($pages > 0 && $page > $pages) $page = $pages;
        return $page;
    }

    private function customerName(object $row): string
    {
        $name = $row->display_name ?? '';
        if ($name === '' || $name === null) $name = $row->user_login ?? '';
        if ($name === '' || $name === null) $name = 'Customer ' . (int) ($row->user_id ?? $row->ID ?? 0);
        return (string) $name;
    }

    private function audit(int $customerId, ?int $keyId, string $action, string $summary): void
    {
        $adminId = (int) session()->get('admin_id');
        AiConnect::log($customerId, $keyId, 'admin', $action, 'admin', 'ok', 'actor admin #' . $adminId . ': ' . $summary, request()->ip());
    }

    private function back(string $key, string $text)
    {
        return redirect('/admin/ai-connect')->with($key, $text)->withHeaders(self::NC);
    }
}
