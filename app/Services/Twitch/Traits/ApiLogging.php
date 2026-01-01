<?php

namespace App\Services\Twitch\Traits;

use App\Services\Twitch\DTOs\ApiLogEntryDTO;
use Illuminate\Support\Facades\Log;

trait ApiLogging
{
    /**
     * Filter sensitive data from arrays before logging
     */
    protected function filterSensitiveData(array $data): array
    {
        $sensitiveKeys = [
            'access_token',
            'refresh_token',
            'client_secret',
            'client_id',
            'authorization',
            'bearer',
            'token',
            'password',
            'secret',
            'key',
        ];

        $filtered = $data;

        foreach ($sensitiveKeys as $key) {
            if (array_key_exists($key, $filtered)) {
                $filtered[$key] = '[FILTERED]';
            }
        }

        // Also filter nested arrays
        foreach ($filtered as $key => $value) {
            if (is_array($value)) {
                $filtered[$key] = $this->filterSensitiveData($value);
            }
        }

        return $filtered;
    }

    protected function logApiCall(ApiLogEntryDTO $logEntry): void
    {
        if (! config('twitch.log_requests', false)) {
            return;
        }

        $filteredParams = $this->filterSensitiveData($logEntry->params);
        $message        = "Twitch API Call: {$logEntry->endpoint} with params: ".json_encode($filteredParams);

        if ($logEntry->method) {
            $message .= " (Method: {$logEntry->method})";
        }

        if ($logEntry->duration) {
            $message .= sprintf(' (Duration: %.2fs)', $logEntry->duration);
        }

        if ($logEntry->error) {
            Log::error($message." Error: {$logEntry->error}");
        } else {
            $responseInfo     = $logEntry->statusCode ? "Status: {$logEntry->statusCode}, " : '';
            $filteredResponse = $logEntry->response ? $this->filterSensitiveData($logEntry->response) : null;
            Log::info($message.' Response: '.$responseInfo.json_encode($filteredResponse));
        }
    }
}
