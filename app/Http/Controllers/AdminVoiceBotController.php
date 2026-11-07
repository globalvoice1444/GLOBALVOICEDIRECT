<?php

namespace App\Http\Controllers;

use App\Jobs\ProvisionVoiceAccount;
use App\Models\AiVoiceAccount;
use App\Services\VoiceAccountProvisioner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Admin > AI Connect > Voice bots. Read-only list plus re-provision and deprovision. Never renders secrets. */
class AdminVoiceBotController extends Controller
{
    const NC = ['Cache-Control' => 'private, no-store, max-age=0'];

    public function index()
    {
        return response()->view('admin.voice-bots', [
            'rows' => $this->rows(),
            'providers' => VoiceAccountProvisioner::providers(),
        ])->withHeaders(self::NC);
    }

    public function reprovision(Request $r)
    {
        $account = $this->account((int) $r->input('user_id'));
        $account->status = 'pending';
        $account->last_error = null;
        $account->save();
        ProvisionVoiceAccount::enqueue((int) $account->user_id, ProvisionVoiceAccount::PUT);
        $account->refresh();
        if ($account->status === 'error') {
            return $this->back('error', $account->last_error ?: 'Could not re-provision the voice account.');
        }
        return $this->back('message', $account->status === 'pending' ? 'Re-provision queued.' : 'Voice account re-provisioned.');
    }

    public function deprovision(Request $r)
    {
        $account = $this->account((int) $r->input('user_id'));
        $uid = (int) $account->user_id;
        if ($account->provisioned_at === null) {
            $account->delete();
            return $this->back('message', 'Voice account removed.');
        }
        $account->status = 'removing';
        $account->last_error = null;
        $account->save();
        ProvisionVoiceAccount::enqueue($uid, ProvisionVoiceAccount::DELETE);
        $left = AiVoiceAccount::where('user_id', $uid)->first();
        if ($left && $left->status === 'error') {
            return $this->back('error', $left->last_error ?: 'Could not remove the voice account.');
        }
        return $this->back('message', $left ? 'Removal queued.' : 'Voice account removed.');
    }

    private function account(int $userId): AiVoiceAccount
    {
        abort_unless($userId > 0, 404);
        $account = AiVoiceAccount::where('user_id', $userId)->first();
        abort_unless($account, 404);
        return $account;
    }

    private function back(string $key, string $text)
    {
        return redirect('/admin/ai-connect/voice-bots')->with($key, $text)->withHeaders(self::NC);
    }

    /** Safe columns only. The provider key and SIP password are not selected. */
    private function rows(): array
    {
        $providers = VoiceAccountProvisioner::providers();
        $records = DB::table('ai_voice_accounts as a')
            ->leftJoin('wpgc_users as u', 'u.ID', '=', 'a.user_id')
            ->orderByDesc('a.id')
            ->get([
                'a.user_id', 'a.account_id', 'a.username', 'a.provider', 'a.enabled', 'a.status',
                'a.last_error', 'a.last_result', 'a.api_key_updated_at', 'a.provisioned_at',
                'a.created_at', 'a.updated_at',
                'u.user_login', 'u.display_name', 'u.user_nicename', 'u.user_email',
                DB::raw("((a.api_key IS NOT NULL AND a.api_key != '') OR (a.provider_credentials IS NOT NULL AND a.provider_credentials != '')) as has_key"),
            ]);

        return $records->map(function ($row) use ($providers) {
            $name = $row->display_name ?: ($row->user_nicename ?: ($row->user_login ?: 'Customer ' . $row->user_id));
            $result = $row->last_result ?: $row->last_error;
            return [
                'user_id' => (int) $row->user_id,
                'account_id' => (string) $row->account_id,
                'name' => (string) $name,
                'login' => $row->user_login ? (string) $row->user_login : '',
                'email' => $row->user_email ? (string) $row->user_email : '',
                'provider' => is_array($providers[$row->provider] ?? null) ? (string) $providers[$row->provider]['label'] : (string) $row->provider,
                'calls_supported' => is_array($providers[$row->provider] ?? null) && !empty($providers[$row->provider]['gateway_supported']),
                'has_key' => (bool) $row->has_key,
                'key_updated' => $this->when($row->api_key_updated_at),
                'username' => (string) $row->username,
                'enabled' => (bool) $row->enabled,
                'status' => (string) $row->status,
                'result' => $result ? (string) $result : '',
                'created' => $this->when($row->created_at),
                'updated' => $this->when($row->updated_at),
                'provisioned' => $this->when($row->provisioned_at),
            ];
        })->all();
    }

    private function when($value): string
    {
        if (!$value) return '';
        return Carbon::parse($value)->tz('America/New_York')->format('M j, Y g:i A') . ' ET';
    }
}
