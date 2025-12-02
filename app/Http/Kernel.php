<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     * Tüm HTTP isteklerinde çalışır.
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Route middleware grupları.
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\CheckAllowedHosts::class, // Domain beyaz liste kontrolü
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Route bazlı middleware tanımları.
     * Örnek: Route::middleware('auth')
     */
    protected $routeMiddleware = [
        'auth'                 => \App\Http\Middleware\Authenticate::class,
        'verified'             => \App\Http\Middleware\EnsureEmailIsVerified::class,
        'throttle'             => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'bindings'             => \Illuminate\Routing\Middleware\SubstituteBindings::class,

        // Özel erişim kontrolleri
        'admin'                => \App\Http\Middleware\IsAdmin::class,
        'isAdmin'              => \App\Http\Middleware\IsAdmin::class,
        'can.viewExportLogs'   => \App\Http\Middleware\CanViewExportLogs::class,
        'role'                 => \App\Http\Middleware\RoleMiddleware::class,
        
        // Cache middleware
        'cache.response'       => \App\Http\Middleware\CacheResponse::class,
    ];
}