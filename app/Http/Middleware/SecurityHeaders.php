<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request with comprehensive security headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip CSP for auth routes to prevent login issues
        if ($request->is('auth/*')) {
            return $response
                // Prevent MIME type sniffing
                ->header('X-Content-Type-Options', 'nosniff')
                // Prevent clickjacking
                ->header('X-Frame-Options', 'DENY')
                // XSS protection
                ->header('X-XSS-Protection', '1; mode=block')
                // Referrer policy
                ->header('Referrer-Policy', 'strict-origin-when-cross-origin')
                // Permissions policy
                ->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        }

        // Build CSP dynamically based on environment
        $csp = $this->buildContentSecurityPolicy($request);

        $response = $response
            // Prevent MIME type sniffing
            ->header('X-Content-Type-Options', 'nosniff')
            // Prevent clickjacking
            ->header('X-Frame-Options', 'DENY')
            // XSS protection
            ->header('X-XSS-Protection', '1; mode=block')
            // Referrer policy
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin')
            // Permissions policy
            ->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()')
            // Content Security Policy
            ->header('Content-Security-Policy', $csp);

        // HSTS (only for HTTPS)
        if ($request->secure()) {
            $hstsConfig        = config('performance.security_headers.hsts', []);
            $maxAge            = $hstsConfig['max_age'] ?? 31536000;
            $includeSubdomains = $hstsConfig['include_subdomains'] ?? true;
            $preload           = $hstsConfig['preload'] ?? false;

            $hstsValue = 'max-age='.$maxAge;
            if ($includeSubdomains) {
                $hstsValue .= '; includeSubDomains';
            }
            if ($preload) {
                $hstsValue .= '; preload';
            }

            $response->header('Strict-Transport-Security', $hstsValue);
        }

        return $response;
    }

    private function buildContentSecurityPolicy(Request $request): string
    {
        // Get current domain for CSP
        $currentHost = $request->getHost();
        $scheme      = $request->getScheme();

        $cspConfig         = config('performance.security_headers.csp', []);
        $additionalSources = $cspConfig['additional_sources'] ?? [];

        // More permissive CSP for development or when using HTTPS
        if (app()->environment('local') || $request->secure()) {
            $baseCsp = [
                "default-src 'self'",
                "img-src 'self' data: https: blob:",
                "connect-src 'self' https://api.twitch.tv wss: https://clips.twitch.tv",
                "media-src 'self' https://clips.twitch.tv https://static-cdn.jtvnw.net",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self' https:",
                "frame-ancestors 'none'",
                'upgrade-insecure-requests',
            ];
        } else {
            $baseCsp = [
                "default-src 'self'",
                "img-src 'self' data: https: blob:",
                "connect-src 'self' https://api.twitch.tv wss: https://clips.twitch.tv",
                "media-src 'self' https://clips.twitch.tv https://static-cdn.jtvnw.net",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'none'",
                'upgrade-insecure-requests',
            ];
        }

        // Add additional form-action sources from config
        $formActionSources = array_filter($additionalSources['form_action'] ?? []);
        if (! empty($formActionSources)) {
            $baseCsp[6] .= ' '.implode(' ', $formActionSources);
        }

        // Font sources
        $fontSrc = ["'self'"];
        if (app()->environment('local')) {
            // Allow Vite HMR fonts in development
            $fontSrc[] = config('app.url').':5173';
        }
        $additionalFontSources = array_filter($additionalSources['font'] ?? []);
        $fontSrc               = array_merge($fontSrc, $additionalFontSources);
        $baseCsp[]             = 'font-src '.implode(' ', $fontSrc);

        // Script sources
        $scriptSrc = ["'self'", "'unsafe-inline'", 'https://cdn.jsdelivr.net', 'https://code.jquery.com'];
        if (app()->environment('local')) {
            // Allow Vite HMR in development
            $scriptSrc[] = config('app.url').':5173';
        }
        $additionalScriptSources = array_filter($additionalSources['script'] ?? []);
        $scriptSrc               = array_merge($scriptSrc, $additionalScriptSources);
        $baseCsp[]               = 'script-src '.implode(' ', $scriptSrc);

        // Style sources
        $styleSrc = ["'self'", "'unsafe-inline'", 'https://cdn.jsdelivr.net'];
        if (app()->environment('local')) {
            // Allow Vite HMR in development
            $styleSrc[] = config('app.url').':5173';
        }
        $additionalStyleSources = array_filter($additionalSources['style'] ?? []);
        $styleSrc               = array_merge($styleSrc, $additionalStyleSources);
        $baseCsp[]              = 'style-src '.implode(' ', $styleSrc);

        // Image sources
        $imgSrc               = ["'self'", 'data:', 'https:', 'blob:'];
        $additionalImgSources = array_filter($additionalSources['img'] ?? []);
        $imgSrc               = array_merge($imgSrc, $additionalImgSources);
        $baseCsp[1]           = 'img-src '.implode(' ', $imgSrc);

        // Connect sources
        $connectSrc               = ["'self'", 'https://api.twitch.tv', 'wss:', 'https://clips.twitch.tv'];
        $additionalConnectSources = array_filter($additionalSources['connect'] ?? []);
        $connectSrc               = array_merge($connectSrc, $additionalConnectSources);
        $baseCsp[2]               = 'connect-src '.implode(' ', $connectSrc);

        return implode('; ', $baseCsp);
    }
}
