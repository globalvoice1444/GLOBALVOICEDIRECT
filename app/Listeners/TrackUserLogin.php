<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/** 2026-11-02 Records last login + login count for portal customers. Never blocks a login. */
class TrackUserLogin
{
    public function handle(Login $event): void
    {
        try {
            $u = $event->user;
            if (!$u || !($u instanceof \App\User)) return;
            $id = (int) $u->getAuthIdentifier(); if ($id <= 0) return;
            $ip = mb_substr((string) request()->ip(), 0, 45); $now = now();
            DB::statement('INSERT INTO user_activity (user_id,last_login_at,login_count,last_login_ip,first_tracked_at,created_at,updated_at) VALUES (?,?,1,?,?,?,?)
                ON DUPLICATE KEY UPDATE last_login_at=VALUES(last_login_at), login_count=login_count+1, last_login_ip=VALUES(last_login_ip), updated_at=VALUES(updated_at)',
                [$id, $now, $ip, $now, $now, $now]);
        } catch (\Throwable $e) { Log::warning('login tracking failed', ['err' => mb_substr($e->getMessage(), 0, 200)]); }
    }
}
