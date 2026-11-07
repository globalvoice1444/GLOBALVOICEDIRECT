<?php

namespace App\Http\Middleware;

use Closure;

/** 404 the Voice bot routes while AI_VOICE_PROVISIONING_ENABLED is off. */
class EnsureVoiceProvisioning
{
    public function handle($request, Closure $next)
    {
        if (!config('ai_connect.voice_provisioning_enabled')) {
            abort(404);
        }
        return $next($request);
    }
}
