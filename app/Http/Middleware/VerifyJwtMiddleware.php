<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class VerifyJwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'JWT bulunamadı'], 401);
        }
        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $request->attributes->add(['jwt_user_id' => $decoded->sub]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'JWT doğrulanamadı'], 401);
        }
        return $next($request);
    }
}
