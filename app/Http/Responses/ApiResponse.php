<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = '', int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    public static function error(string $message = 'Error.', int $code = Response::HTTP_BAD_REQUEST, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'errors' => $errors,
            'message' => $message,
        ], $code);
    }

    public static function created(mixed $data = null, string $message = 'Resource created successfully.', int $code = Response::HTTP_CREATED): JsonResponse
    {
        return self::success($data, $message, $code);
    }

    public static function notFound(mixed $message = 'Not Found.', int $code = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return self::error($message, Response::HTTP_NOT_FOUND, $code);
    }

    public static function unAuthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, Response::HTTP_UNAUTHORIZED);
    }

    public static function forbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return self::error($message, Response::HTTP_FORBIDDEN);
    }
}
