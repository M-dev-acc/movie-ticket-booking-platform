<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, string $message = "Success", int $status = 200) : JsonResponse {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error(array $errors = [], string $message = "Success", int $status = 400) : JsonResponse {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public static function paginated($data, array $meta, string $message = "Success", int $status = 200) : JsonResponse {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $status);
    }
}
