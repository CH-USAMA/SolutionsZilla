<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    /**
     * Check the health of the application and its dependencies.
     */
    public function check()
    {
        $status = [
            'status' => 'healthy',
            'components' => [
                'database' => 'ok',
                'cache' => 'ok',
                'storage' => 'ok',
            ],
            'timestamp' => now()->toIso8601String(),
            'environment' => config('app.env'),
        ];

        // Check Database
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $status['status'] = 'unhealthy';
            $status['components']['database'] = 'error: ' . $e->getMessage();
            Log::critical('Health Check: Database connection failed.');
        }

        // Check Cache
        try {
            cache()->put('health_check', true, 1);
            if (!cache()->get('health_check')) {
                throw new \Exception('Cache get/set failed');
            }
        } catch (\Exception $e) {
            $status['status'] = 'degraded';
            $status['components']['cache'] = 'error: ' . $e->getMessage();
            Log::warning('Health Check: Cache check failed.');
        }

        // Check Storage
        if (!is_writable(storage_path('logs')) || !is_writable(storage_path('framework/cache'))) {
            $status['status'] = 'unhealthy';
            $status['components']['storage'] = 'error: Storage directories not writable';
            Log::critical('Health Check: Storage directories not writable.');
        }

        $httpCode = ($status['status'] === 'unhealthy') ? 503 : 200;

        return response()->json($status, $httpCode);
    }
}
