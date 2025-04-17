<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        //
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            // Validation exception
            if ($e instanceof ValidationException) {
                return response()->json([
                    'status'  => false,
                    'code'    => 422,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }

            // Authentication error
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'status'  => false,
                    'code'    => 401,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // Not found
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'status'  => false,
                    'code'    => 404,
                    'message' => 'Route not found',
                ], 404);
            }

            // Http-specific exceptions
            if ($e instanceof HttpExceptionInterface) {
                return response()->json([
                    'status'  => false,
                    'code'    => $e->getStatusCode(),
                    'message' => $e->getMessage() ?: 'HTTP Error',
                ], $e->getStatusCode());
            }

            if ($e instanceof BusinessException) {
                return response()->json([
                    'status'  => false,
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                    'errors'  => $e->getErrors(),
                ], 200);
            }

            // Fallback for unexpected errors
            return response()->json([
                'status'  => false,
                'code'    => 500,
                'message' => config('app.debug') ? $e->getMessage() : 'Server error',
                'trace'   => config('app.debug') ? collect($e->getTrace())->take(3) : [],
            ], 500);
        }

        return parent::render($request, $e);
    }
}
