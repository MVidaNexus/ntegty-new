<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PageView;

class TrackPageViews
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track GET requests and HTML responses
        if ($request->isMethod('GET') && !$request->ajax() && !$request->wantsJson()) {
            // Don't track admin, api, or asset requests
            $path = $request->path();
            if (!str_starts_with($path, 'admin') && 
                !str_starts_with($path, 'api') && 
                !str_starts_with($path, 'livewire') &&
                !preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf)$/i', $path)) {
                
                // Determine page type
                $pageType = $this->detectPageType($path);
                $governorateId = $this->extractGovernorateId($request);
                
                // Record asynchronously to not slow down response
                try {
                    PageView::record($path, $pageType, $governorateId);
                } catch (\Exception $e) {
                    // Silently fail - don't break the page for tracking errors
                    \Log::error('PageView tracking error: ' . $e->getMessage());
                }
            }
        }

        return $response;
    }

    private function detectPageType(string $path): ?string
    {
        if ($path === '' || $path === '/') {
            return 'home';
        }
        
        if (str_contains($path, '/search') || str_contains($path, 'search')) {
            return 'search';
        }
        
        if (preg_match('/\/\d+$/', $path)) {
            return 'result';
        }
        
        if (str_contains($path, '/all')) {
            return 'all_results';
        }
        
        if (str_contains($path, 'preparatory') || str_contains($path, 'secondary')) {
            return 'governorate';
        }
        
        if (str_contains($path, 'egypt')) {
            return 'country';
        }
        
        return 'other';
    }

    private function extractGovernorateId(Request $request): ?int
    {
        $governorate = $request->route('governorate');
        if ($governorate && is_object($governorate)) {
            return $governorate->id;
        }
        return null;
    }
}
