<?php

declare(strict_types=1);

// =============================================================================
// EMAIL MASKING
// =============================================================================
if (! function_exists('mask_email')) {
    /**
     * Mask an email address for privacy-safe display (irreversible).
     *
     * Options:
     *  - mask_char: string (default: '•')
     *  - visible_local: int (default: 1)
     *  - visible_local_end: int (default: 1)
     *  - visible_domain_start: int (default: 1)
     *  - visible_domain_end: int (default: 1)
     *  - mask_domain_before_tld: bool (default: true)
     *
     * @param  array<string,mixed>  $options
     */
    function mask_email(?string $email, array $options = []): string
    {
        if (empty($email)) {
            return '';
        }

        $maskChar            = $options['mask_char'] ?? '•';
        $visibleLocalStart   = isset($options['visible_local']) ? max(0, (int) $options['visible_local']) : ($options['visible_local_start'] ?? 1);
        $visibleLocalEnd     = array_key_exists('visible_local_end', $options) ? (int) $options['visible_local_end'] : (isset($options['visible_local']) ? 0 : 1);
        $visibleDomainStart  = $options['visible_domain_start'] ?? 1;
        $visibleDomainEnd    = $options['visible_domain_end'] ?? 1;
        $maskDomainBeforeTld = $options['mask_domain_before_tld'] ?? true;

        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        // Mask local part
        $localLen    = mb_strlen($local);
        $localMasked = $localLen <= ($visibleLocalStart + $visibleLocalEnd)
            ? str_repeat($maskChar, max(1, $localLen))
            : mb_substr($local, 0, $visibleLocalStart)
                .str_repeat($maskChar, max(1, $localLen - $visibleLocalStart - $visibleLocalEnd))
                .($visibleLocalEnd > 0 ? mb_substr($local, -$visibleLocalEnd) : '');

        // Mask domain part
        if ($maskDomainBeforeTld && ($lastDot = mb_strrpos($domain, '.')) !== false) {
            $domainName       = mb_substr($domain, 0, $lastDot);
            $tld              = mb_substr($domain, $lastDot);
            $domainLen        = mb_strlen($domainName);
            $domainMaskedName = $domainLen <= ($visibleDomainStart + $visibleDomainEnd)
                ? str_repeat($maskChar, max(1, $domainLen))
                : mb_substr($domainName, 0, $visibleDomainStart)
                    .str_repeat($maskChar, max(1, $domainLen - $visibleDomainStart - $visibleDomainEnd))
                    .mb_substr($domainName, -$visibleDomainEnd);
            $domainMasked = $domainMaskedName.$tld;
        } else {
            $domainLen    = mb_strlen($domain);
            $domainMasked = $domainLen <= ($visibleDomainStart + $visibleDomainEnd)
                ? $domain
                : mb_substr($domain, 0, $visibleDomainStart)
                    .str_repeat($maskChar, max(1, $domainLen - $visibleDomainStart - $visibleDomainEnd))
                    .mb_substr($domain, -$visibleDomainEnd);
        }

        return $localMasked.'@'.$domainMasked;
    }
}

// =============================================================================
// IP MASKING
// =============================================================================
if (! function_exists('mask_ip')) {
    /**
     * Mask an IP address for privacy-safe logging.
     * IPv4: last octet replaced with '0'. IPv6: keep first 4 groups, append '::'.
     * Returns null for empty input or invalid IP.
     */
    function mask_ip(?string $ip): ?string
    {
        if (empty($ip)) {
            return null;
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            if (count($parts) === 4) {
                $parts[3] = '0';

                return implode('.', $parts);
            }
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);

            return implode(':', array_slice($parts, 0, 4)).'::';
        }

        return null;
    }
}

// =============================================================================
// IP PSEUDONYMIZATION (GDPR COMPLIANT)
// =============================================================================
if (! function_exists('pseudonymize_ip')) {
    /**
     * Pseudonymize IP address with keyed hash (GDPR compliant).
     * Uses HMAC-SHA256 with app key for deterministic, irreversible pseudonymization.
     * This allows for rate limiting and analytics while maintaining privacy.
     */
    function pseudonymize_ip(?string $ip): ?string
    {
        if (empty($ip)) {
            return null;
        }

        $salt = config('app.key');

        return hash_hmac('sha256', $ip, $salt);
    }
}
