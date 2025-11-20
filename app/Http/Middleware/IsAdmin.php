<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Debug logging
        \Illuminate\Support\Facades\Log::info('IsAdmin Middleware', [
            'user' => $request->user() ? $request->user()->id : 'null',
            'role' => $request->user() ? $request->user()->role : 'null',
            'headers' => $request->headers->all()
        ]);

        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        return $next($request);
    }
}
