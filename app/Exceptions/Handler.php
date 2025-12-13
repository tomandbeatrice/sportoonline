<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
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
            // Log critical errors to external service in production
            if (app()->environment('production')) {
                \Log::critical($e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        });

        // Handle common HTTP exceptions with JSON responses
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Resource not found',
                    'message' => 'The requested resource was not found.'
                ], 404);
            }
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Method not allowed',
                    'message' => 'The HTTP method is not allowed for this endpoint.'
                ], 405);
            }
        });

        $this->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Validation failed',
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors()
                ], 422);
            }
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