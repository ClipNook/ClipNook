<?php

namespace App\Http\Middleware;

use App\Services\Monitoring\PerformanceMonitor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitoring
{
    public function __construct(private PerformanceMonitor $monitor) {}

    /**
     * Handle an incoming request with performance monitoring.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime      = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Record response time metric
        $route = $request->route()?->getName() ?? $request->path();
        $this->monitor->recordResponseTime($route, $responseTime);

        // Add performance headers
        $response->headers->set('X-Response-Time', round($responseTime, 2).'ms');
        $response->headers->set('X-Memory-Usage', $this->formatBytes(memory_get_usage(true)));
        $response->headers->set('X-Memory-Peak', $this->formatBytes(memory_get_peak_usage(true)));

        return $response;
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i     = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
