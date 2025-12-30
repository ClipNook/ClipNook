<?php

namespace App\Services\Twitch\Traits;

use Illuminate\Support\Facades\Log;

trait ApiLogging
{
    protected function logApiCall(string $endpoint, array $params, ?array $response, ?string $error = null): void
    {
        if (! config('twitch.log_requests', false)) {
            return;
        }

        $message = "Twitch API Call: {$endpoint} with params: ".json_encode($params);
        if ($error) {
            Log::error($message." Error: {$error}");
        } else {
            Log::info($message.' Response: '.json_encode($response));
        }
    }
}
