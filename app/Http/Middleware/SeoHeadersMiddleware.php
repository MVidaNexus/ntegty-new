<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeoHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security & SEO Headers
        $path = $request->getPathInfo();
        $isPrivate = str_contains($path, '/student/') || 
                     str_contains($path, '/result/') || 
                     str_contains($path, '/search') || 
                     str_contains($path, '/admin') || 
                     str_contains($path, '/dashboard') || 
                     preg_match('/\/[0-9]+$/', $path) || 
                     preg_match('/\/term1\/[0-9]+$/', $path) || 
                     preg_match('/\/term2\/[0-9]+$/', $path) || 
                     preg_match('/\/all\/[0-9]+$/', $path);

        if ($isPrivate) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        } else {
            $response->headers->set('X-Robots-Tag', 'index, follow');
        }
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // HSTS (Force HTTPS) - 1 year max-age
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // Permissions Policy (Data Minimization)
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=()');

        // Content Security Policy (Prevent XSS and unauthorized resource loading)
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://*.google.com https://*.google-analytics.com https://*.googletagmanager.com https://*.googleadservices.com https://*.doubleclick.net https://googleads.g.doubleclick.net https://pagead2.googlesyndication.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; " .
               "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self' https:; " .
               "frame-src 'self' https:;";
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
