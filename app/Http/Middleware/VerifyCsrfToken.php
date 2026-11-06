<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'email/unsubscribe', // 2026-11-02 one-click unsubscribe (signed URL)
        'ec/unsubscribe', // 2026-11-02 customer campaign one-click unsubscribe (signed URL)
    ];
}
