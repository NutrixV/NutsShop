<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * Class HealthController
 * 
 * Controller for health check endpoints
 */
class HealthController extends Controller
{
    /**
     * Return a health check response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
} 