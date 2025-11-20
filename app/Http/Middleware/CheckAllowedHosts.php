<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAllowedHosts
{
    public function handle(Request $request, Closure $next)
    {
        $allowed = array_map('trim', explode(',', env('ALLOWED_HOSTS', 'localhost')));
        if (!in_array($request->getHost(), $allowed, true)) {
            abort(403, 'Host not allowed');
        }
        return $next($request);
    }
}
