<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    /**
     * Render an exception into an HTTP response for API requests.
     */
    public static function render(Request $request, Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return ApiResponse::validationError($e->errors(), $e->getMessage());
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return ApiResponse::notFound('Resource not found');
        }

        if ($e instanceof AuthenticationException) {
            return ApiResponse::unauthorized('Unauthenticated');
        }

        if ($e instanceof HttpException) {
            return ApiResponse::error(
                $e->getMessage() ?: 'Server error',
                $e->getStatusCode()
            );
        }

        if (config('app.debug')) {
            return ApiResponse::serverError($e->getMessage());
        }

        return ApiResponse::serverError('An unexpected error occurred');
    }
}
