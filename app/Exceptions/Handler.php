<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->environment('production')) {
                Log::critical($e->getMessage(), ['exception' => $e]);
            }
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return response()->json(['error' => 'Resource not found'], 404);
        });
        
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e) {
            return response()->json(['error' => 'Method not allowed'], 405);
        });
        
        $this->renderable(function (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'errors' => $e->errors()], 422);
        });
    }

    /**
     * API isteklerinde AuthenticationException için JSON yanıtı döndür.
     * Bu, 'Route [login] not defined' hatasını önler.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'error' => 'Bu işlem için giriş yapmanız gerekiyor.'
            ], 401);
        }

        // Web istekleri için varsayılan davranış (login sayfasına yönlendir)
        // Eğer login rotası yoksa ana sayfaya yönlendir
        try {
            return redirect()->guest(route('login'));
        } catch (\Exception $e) {
            return redirect('/');
        }
    }
}