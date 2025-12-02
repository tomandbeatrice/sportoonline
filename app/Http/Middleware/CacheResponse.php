<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cache API Responses Middleware
 * 
 * Caches GET requests for specified duration
 */
class CacheResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, int $ttl = 3600): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }
        
        // Generate cache key from URL and query params
        $cacheKey = 'api.response.' . md5($request->fullUrl());
        
        // Try to get from cache
        $cachedResponse = Cache::get($cacheKey);
        
        if ($cachedResponse) {
            return response()->json($cachedResponse)
                ->header('X-Cache-Hit', 'true');
        }
        
        // Get fresh response
        $response = $next($request);
        
        // Cache successful responses only
        if ($response->getStatusCode() === 200) {
            $content = json_decode($response->getContent(), true);
            Cache::put($cacheKey, $content, $ttl);
        }
        
        return $response->header('X-Cache-Hit', 'false');
    }
}
