<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent browsers from MIME-sniffing the content type
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Block clickjacking — page cannot be embedded in an iframe
        $response->headers->set('X-Frame-Options', 'DENY');

        // Enable browser XSS filter (older browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Force HTTPS in production only
        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Disable features TekioTask doesn't use
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()'
        );

        // CSP — adjust if you add external CDNs (e.g. fonts.googleapis.com)
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline'; " .   // inline JS needed for Blade
            "style-src 'self' 'unsafe-inline'; " .    // inline CSS needed for Blade
            "img-src 'self' data:; " .
            "font-src 'self'; " .
            "connect-src 'self'; " .
            "frame-ancestors 'none';"
        );

        return $response;
    }
}
