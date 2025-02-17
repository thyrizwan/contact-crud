<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected function sendResponse($data, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function sendError(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}
