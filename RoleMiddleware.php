public function handle($request, Closure $next, $role) {
    if ($request->user()->role !== $role) {
        return response()->json(['error' => 'Yetkisiz'], 403);
    }
    return $next($request);
}