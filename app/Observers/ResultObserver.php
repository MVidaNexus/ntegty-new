<?php

namespace App\Observers;

use App\Models\Result;
use App\Services\CacheService;
use App\Http\Middleware\CacheResponse;
use App\Http\Controllers\SitemapController;

class ResultObserver
{
    /**
     * Handle the Result "created" event.
     */
    public function created(Result $result): void
    {
        $this->invalidateCache($result);
    }

    /**
     * Handle the Result "updated" event.
     */
    public function updated(Result $result): void
    {
        $this->invalidateCache($result);
    }

    /**
     * Handle the Result "deleted" event.
     */
    public function deleted(Result $result): void
    {
        $this->invalidateCache($result);
    }

    /**
     * Invalidate related cache
     */
    private function invalidateCache(Result $result): void
    {
        // Invalidate specific result cache
        $key = CacheService::PREFIX_RESULTS . "{$result->exam_type_id}:{$result->academic_year_id}:{$result->seat_number}";
        if ($result->branch_id) {
            $key .= ":{$result->branch_id}";
        }
        \Illuminate\Support\Facades\Cache::forget($key);
        
        // Invalidate stats
        CacheService::invalidateStats();
        
        // Invalidate page cache for related pages
        CacheResponse::invalidateAll();
        
        // Invalidate sitemap cache (لتحديث الخرائط تلقائياً)
        $this->invalidateSitemapCache($result);
    }

    /**
     * Invalidate sitemap cache for new results
     */
    private function invalidateSitemapCache(Result $result): void
    {
        // مسح كل كاش الخرائط باستخدام CacheService
        CacheService::invalidateSitemap();
    }
}
