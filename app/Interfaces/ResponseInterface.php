<?php

namespace App\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;

interface ResponseInterface
{
    /**
     * Success response (200)
     *
     * @param array $data
     * @return JsonResponse
     */
    public function success(array $data): JsonResponse;

    /**
     * Validation errors response (422)
     *
     * @param MessageBag $validationErrors
     * @return JsonResponse
     */
    public function validationError(MessageBag $validationErrors): JsonResponse;

    /**
     * Failed response (500)
     *
     * @param string $message
     * @return JsonResponse
     */
    public function failed(string $message = 'Internal Server Error'): JsonResponse;
}