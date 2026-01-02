<?php

declare(strict_types=1);

if (! function_exists('csp_nonce')) {
    /**
     * Get the CSP nonce for the current request.
     */
    function csp_nonce(): string
    {
        return request()->attributes->get('csp_nonce', '');
    }
}
