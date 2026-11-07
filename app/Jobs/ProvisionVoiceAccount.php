<?php

namespace App\Jobs;

use App\Models\AiVoiceAccount;
use App\Services\VoiceAccountProvisioner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/** Provisions or removes one customer's voice account. Carries only the user id, never secrets. */
class ProvisionVoiceAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const PUT = 'put';
    public const DELETE = 'delete';

    public int $tries = 1;
    public int $timeout = 30;

    public function __construct(public int $userId, public string $action) {}

    public function handle(VoiceAccountProvisioner $provisioner): void
    {
        $account = AiVoiceAccount::where('user_id', $this->userId)->first();
        if (!$account) return;

        if ($this->action === self::DELETE) {
            $result = $provisioner->delete($account);
            if ($result->ok) {
                AiVoiceAccount::where('user_id', $this->userId)->delete();
                return;
            }
            $message = mb_substr($result->message, 0, 255);
            AiVoiceAccount::where('user_id', $this->userId)->update([
                'status' => 'error',
                'last_error' => $message,
                'last_result' => $message,
                'updated_at' => now(),
            ]);
            return;
        }

        $result = $provisioner->put($account);
        $message = mb_substr($result->message, 0, 255);
        $update = [
            'status' => $result->ok ? 'active' : 'error',
            'last_error' => $result->ok ? null : $message,
            'last_result' => $result->ok ? ($result->httpStatus === 201 ? 'Created' : 'Updated') : $message,
            'updated_at' => now(),
        ];
        if ($result->ok) {
            $update['provisioned_at'] = now();
            if ($result->touchedApiKey) {
                $update['api_key_dirty'] = false;
                $update['credentials_dirty'] = false;
                $update['api_key_on_gateway'] = $result->apiKeyOnGateway;
            }
        }
        AiVoiceAccount::where('user_id', $this->userId)->update($update);
    }

    public function failed(\Throwable $e): void
    {
        Log::warning('voice bot provision job failed', [
            'user_id' => $this->userId,
            'action' => $this->action,
            'error' => $e::class,
        ]);
        AiVoiceAccount::where('user_id', $this->userId)->update([
            'status' => 'error',
            'last_error' => 'Provisioning failed. Try again.',
            'last_result' => 'Provisioning failed. Try again.',
            'updated_at' => now(),
        ]);
    }

    /** Queue the job. If the queue cannot accept it, run it now. */
    public static function enqueue(int $userId, string $action): void
    {
        $job = new self($userId, $action);
        $dispatcher = app(Dispatcher::class);
        if (config('queue.default') === 'sync') {
            $dispatcher->dispatchSync($job);
            return;
        }
        try {
            $dispatcher->dispatch($job);
        } catch (\Throwable $e) {
            Log::warning('voice bot queue unavailable, running sync', [
                'user_id' => $userId,
                'action' => $action,
                'error' => $e::class,
            ]);
            $dispatcher->dispatchSync($job);
        }
    }
}
