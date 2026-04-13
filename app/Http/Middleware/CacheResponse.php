<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Cache prefix for page cache
     */
    const CACHE_PREFIX = 'page_cache:';

    /**
     * Pages that should NOT be cached
     */
    protected array $excludedPaths = [
        'admin*',
        'dashboard*',
        'login',
        'logout',
        'register',
        'filament*',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if caching is enabled
        if (!$this->isCacheEnabled()) {
            return $next($request);
        }

        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Don't cache for authenticated admin users
        if (auth()->check()) {
            return $next($request);
        }

        // Don't cache excluded paths
        foreach ($this->excludedPaths as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // Get cache duration for this page type
        $ttl = $this->getCacheDuration($request);
        
        // If TTL is 0, don't cache
        if ($ttl <= 0) {
            return $next($request);
        }

        // Generate cache key based on full URL
        $cacheKey = $this->generateCacheKey($request);

        // Try to get from cache
        $cachedResponse = Cache::get($cacheKey);
        
        if ($cachedResponse) {
            return response($cachedResponse['content'])
                ->header('Content-Type', $cachedResponse['content_type'])
                ->header('X-Cache', 'HIT')
                ->header('X-Cache-Key', substr($cacheKey, 0, 50));
        }

        // Get fresh response
        $response = $next($request);

        // Only cache successful HTML responses
        if ($response->getStatusCode() === 200 && 
            str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            
            Cache::put($cacheKey, [
                'content' => $response->getContent(),
                'content_type' => $response->headers->get('Content-Type'),
                'cached_at' => now()->toIso8601String(),
            ], $ttl);

            $response->header('X-Cache', 'MISS');
            $response->header('X-Cache-TTL', $ttl);
        }

        return $response;
    }

    /**
     * Check if page caching is enabled
     */
    protected function isCacheEnabled(): bool
    {
        return (bool) Setting::get('cache_enabled', true);
    }

    /**
     * Generate cache key for a request
     */
    protected function generateCacheKey(Request $request): string
    {
        $url = $request->fullUrl();
        return self::CACHE_PREFIX . md5($url);
    }

    /**
     * Get cache duration based on page type from database settings
     */
    protected function getCacheDuration(Request $request): int
    {
        $path = $request->path();

        // Check specific patterns and get from database
        if ($path === '/' || $path === '') {
            return (int) Setting::get('cache_home', 600);
        }

        if (str_contains($path, '/all')) {
            return (int) Setting::get('cache_all_results', 300);
        }

        if (preg_match('/^result\/\d+/', $path)) {
            return (int) Setting::get('cache_result', 1800);
        }

        if (str_contains($path, 'preparatory') && !str_contains($path, '/all')) {
            // Check if it's governorate search page (has slug after preparatory)
            if (preg_match('/preparatory\/[^\/]+$/', $path)) {
                return (int) Setting::get('cache_governorate', 300);
            }
            return (int) Setting::get('cache_preparatory', 600);
        }

        if (str_contains($path, 'secondary')) {
            return (int) Setting::get('cache_secondary', 600);
        }

        if (str_contains($path, 'diplomas')) {
            return (int) Setting::get('cache_diplomas', 600);
        }

        // Country or exam index pages
        if (preg_match('/^(egypt|iraq|libya|sudan|palestine|yemen|jordan|syria)$/', $path)) {
            return (int) Setting::get('cache_index', 600);
        }

        return (int) Setting::get('cache_default', 300);
    }

    /**
     * Invalidate all page cache
     */
    public static function invalidateAll(): int
    {
        return \App\Services\CacheService::deleteByPattern(self::CACHE_PREFIX . '*');
    }

    /**
     * Invalidate page cache by pattern
     */
    public static function invalidateByPattern(string $pattern): int
    {
        return \App\Services\CacheService::deleteByPattern(self::CACHE_PREFIX . $pattern);
    }
}
