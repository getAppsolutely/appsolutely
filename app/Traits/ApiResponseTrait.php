<?php

declare(strict_types=1);

namespace App\Traits;

use App\Exceptions\BusinessException;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function success($data = null, string $message = 'Success', int $code = 0): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ], 200);
    }

    protected function error(string $message = 'Business Error', int $code = 1000, $errors = []): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'code'    => $code,
            'message' => $message,
            'errors'  => $errors,
        ], 200); // always 200 for business logic
    }

    protected function failValidation($errors): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'code'    => 422,
            'message' => 'Validation failed',
            'errors'  => $errors,
        ], 422);
    }

    protected function failAuth($message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'code'    => 401,
            'message' => $message,
        ], 401);
    }

    protected function failForbidden($message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'code'    => 403,
            'message' => $message,
        ], 403);
    }

    protected function failServer($message = 'Server error', int $code = 500): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'code'    => $code,
            'message' => $message,
        ], 500);
    }

    protected function flattenJson($data): JsonResponse
    {
        return response()->json($data);
    }

    /**
     * @throws BusinessException
     */
    protected function throwBusinessError(string $message, int $code = 1000, array $errors = []): never
    {
        throw new BusinessException($message, $code, $errors);
    }
}
