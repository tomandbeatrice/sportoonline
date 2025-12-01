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
            //
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