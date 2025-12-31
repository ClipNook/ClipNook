<?php

namespace App\Services\Twitch\Traits;

use App\Services\Twitch\DTOs\ApiLogEntryDTO;
use Illuminate\Support\Facades\Log;

trait ApiLogging
{
    protected function logApiCall(ApiLogEntryDTO $logEntry): void
    {
        if (! config('twitch.log_requests', false)) {
            return;
        }

        $message = "Twitch API Call: {$logEntry->endpoint} with params: ".json_encode($logEntry->params);

        if ($logEntry->method) {
            $message .= " (Method: {$logEntry->method})";
        }

        if ($logEntry->duration) {
            $message .= sprintf(' (Duration: %.2fs)', $logEntry->duration);
        }

        if ($logEntry->error) {
            Log::error($message." Error: {$logEntry->error}");
        } else {
            $responseInfo = $logEntry->statusCode ? "Status: {$logEntry->statusCode}, " : '';
            Log::info($message.' Response: '.$responseInfo.json_encode($logEntry->response));
        }
    }
}
