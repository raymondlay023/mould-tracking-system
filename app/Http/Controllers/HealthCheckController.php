<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthCheckController extends Controller
{
    /**
     * Health check endpoint for monitoring systems.
     */
    public function __invoke(): JsonResponse
    {
        $status = 'healthy';
        $checks = [];

        // Check database connectivity
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'ok';
        } catch (\Exception $e) {
            $checks['database'] = 'error: ' . $e->getMessage();
            $status = 'unhealthy';
        }

        // Check application status
        $checks['application'] = 'running';
        $checks['version'] = config('app.version', '1.0.0');
        $checks['environment'] = config('app.env');

        // Check disk space (optional, can be slow)
        if (function_exists('disk_free_space')) {
            $freeSpace = disk_free_space(base_path());
            $checks['disk_space_mb'] = $freeSpace ? round($freeSpace / 1024 / 1024, 2) : 'unknown';
        }

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ], $status === 'healthy' ? 200 : 503);
    }
}
