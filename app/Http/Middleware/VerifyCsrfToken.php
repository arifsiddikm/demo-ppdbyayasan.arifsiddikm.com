<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs yang dikecualikan dari verifikasi CSRF.
     * Midtrans/Riplabs callback tidak bisa kirim CSRF token.
     */
    protected $except = [
        'bayar/callback',
        'bayar/onprogressmidtrans',
    ];
}
