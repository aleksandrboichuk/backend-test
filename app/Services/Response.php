<?php

namespace App\Services;

use App\Interfaces\ResponseInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;

class Response implements ResponseInterface
{
    /**
     * @inheritdoc
     */
    public function success(array $data, string $dataKey = 'data'): JsonResponse
    {
        return response()->json([
            $dataKey => $data
        ]);
    }

    /**
     * @inheritdoc
     */
    public function validationError(MessageBag $validationErrors): JsonResponse
    {
        return response()->json([
            'errors' => $validationErrors
        ], 422);
    }

    /**
     * @inheritdoc
     */
    public function failed(string $message = 'Internal Server Error'): JsonResponse
    {
        return response()->json([
            'message' => $message
        ], 500);
    }
}