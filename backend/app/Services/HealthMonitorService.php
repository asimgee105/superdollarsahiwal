<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class HealthMonitorService
{
    /**
     * Fetch status indicators of all platform elements.
     */
    public function getHealthMetrics(): array
    {
        $dbStatus = 'healthy';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'failed: '.$e->getMessage();
        }

        $freeDisk = disk_free_space('/');
        $totalDisk = disk_total_space('/');
        $diskUsagePercent = $totalDisk > 0 ? (($totalDisk - $freeDisk) / $totalDisk) * 100 : 0;

        return [
            'app_version' => '12.0.0-Ultimate',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database' => $dbStatus,
            'disk_free_gb' => round($freeDisk / (1024 * 1024 * 1024), 2),
            'disk_usage_percent' => round($diskUsagePercent, 2),
            'cache_driver' => config('cache.default'),
            'queue_connection' => config('queue.default'),
        ];
    }
}
