<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * CSRF korumasından muaf tutulacak route'lar.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Örnek: 'admin/segment/export'
    ];
}