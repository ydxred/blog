<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * 覆盖默认的 XSRF-TOKEN cookie 名（去框架指纹）。
     * 前端 CSRF 走 <meta name="csrf-token"> + fetch，不依赖此 cookie，改名安全。
     */
    protected function newCookie($request, $config)
    {
        return new Cookie(
            'ydxred_csrf',
            $request->session()->token(),
            $this->availableAt(60 * $config['lifetime']),
            $config['path'],
            $config['domain'],
            $config['secure'],
            false,
            false,
            $config['same_site'] ?? null,
            $config['partitioned'] ?? false
        );
    }
}
