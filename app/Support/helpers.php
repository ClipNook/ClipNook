<?php

declare(strict_types=1);

if (! function_exists('mask_email')) {
    /**
     * Return a safely masked email for display (non-reversible).
     *
     * Configuration options (defaults shown):
     *  - mask_char: string (default: '•')
     *  - visible_local: int (default: 1) - chars visible at the start of the local part
     *  - visible_local_end: int (default: 1) - chars visible at the end of the local part
     *  - visible_domain_start: int (default: 1)
     *  - visible_domain_end: int (default: 1)
     *  - mask_domain_before_tld: bool (default: true) - mask the domain but keep the TLD
     *
     * Purpose: keep a small, consistent amount of information visible for UI/logging
     * while hiding the rest for privacy. Invalid or empty input returns an empty string.
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

        $parts  = explode('@', $email, 2);
        $local  = $parts[0] ?? '';
        $domain = $parts[1] ?? '';

        $localLen = mb_strlen($local);
        if ($localLen <= ($visibleLocalStart + $visibleLocalEnd)) {
            $localMasked = str_repeat($maskChar, max(1, $localLen));
        } else {
            $localMasked = mb_substr($local, 0, $visibleLocalStart)
                .str_repeat($maskChar, max(1, $localLen - $visibleLocalStart - $visibleLocalEnd))
                .($visibleLocalEnd > 0 ? mb_substr($local, -$visibleLocalEnd) : '');
        }

        if ($maskDomainBeforeTld && ($lastDot = mb_strrpos($domain, '.')) !== false) {
            $domainName = mb_substr($domain, 0, $lastDot);
            $tld        = mb_substr($domain, $lastDot);

            $domainLen = mb_strlen($domainName);
            if ($domainLen <= ($visibleDomainStart + $visibleDomainEnd)) {
                $domainMaskedName = str_repeat($maskChar, max(1, $domainLen));
            } else {
                $domainMaskedName = mb_substr($domainName, 0, $visibleDomainStart)
                    .str_repeat($maskChar, max(1, $domainLen - $visibleDomainStart - $visibleDomainEnd))
                    .mb_substr($domainName, -$visibleDomainEnd);
            }

            $domainMasked = $domainMaskedName.$tld;
        } else {
            $domainLen = mb_strlen($domain);
            if ($domainLen <= ($visibleDomainStart + $visibleDomainEnd)) {
                $domainMasked = $domain;
            } else {
                $domainMasked = mb_substr($domain, 0, $visibleDomainStart)
                    .str_repeat($maskChar, max(1, $domainLen - $visibleDomainStart - $visibleDomainEnd))
                    .mb_substr($domain, -$visibleDomainEnd);
            }
        }

        return $localMasked.'@'.$domainMasked;
    }
}

if (! function_exists('mask_ip')) {
    /**
     * Mask an IP address for minimal, non-reversible logging.
     *
     * IPv4: replace last octet with '0'. IPv6: keep first 4 groups and append '::'.
     * Returns null for empty input.
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

        return mb_substr($ip, 0, 32);
    }
}

if (! function_exists('hash_user_agent')) {
    /**
     * Return a deterministic SHA-256 hash of a User-Agent string for privacy-safe storage.
     */
    function hash_user_agent(?string $ua): ?string
    {
        if (empty($ua)) {
            return null;
        }

        return hash('sha256', $ua);
    }
}

if (! function_exists('ui_resolve_link')) {
    /**
     * Resolve a navigation item into an href string.
     *
     * Accepts an array with keys 'route' (+ optional 'params') or 'href'.
     * Falls back to '#' when resolution fails; never throws.
     *
     * @param  array<string,mixed>  $item
     */
    function ui_resolve_link(array $item): string
    {
        $link = '#';

        if (! empty($item['route'])) {
            try {
                $link = \Illuminate\Support\Facades\Route::has($item['route'])
                    ? route($item['route'], $item['params'] ?? [])
                    : '#';
            } catch (\Throwable) {
                $link = '#';
            }
        } elseif (! empty($item['href'])) {
            $href = (string) $item['href'];
            if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://') || str_starts_with($href, '#')) {
                $link = $href;
            } else {
                try {
                    $link = \Illuminate\Support\Facades\Route::has($href)
                        ? route($href)
                        : $href;
                } catch (\Throwable) {
                    $link = $href;
                }
            }
        }

        return $link;
    }
}
