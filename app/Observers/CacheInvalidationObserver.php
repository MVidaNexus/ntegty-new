<?php

namespace App\Observers;

use App\Services\CacheService;

class CacheInvalidationObserver
{
    /**
     * Handle any model "saved" event.
     */
    public function saved($model): void
    {
        $this->invalidate($model);
    }

    /**
     * Handle any model "deleted" event.
     */
    public function deleted($model): void
    {
        $this->invalidate($model);
    }

    /**
     * Invalidate cache based on model type
     */
    private function invalidate($model): void
    {
        $class = get_class($model);
        
        // Models that affect sitemap
        $sitemapAffectingModels = [
            \App\Models\Country::class,
            \App\Models\ExamType::class,
            \App\Models\Governorate::class,
            \App\Models\Result::class,
            \App\Models\ExamBranch::class,
        ];
        
        // Invalidate sitemap cache if relevant model changed
        if (in_array($class, $sitemapAffectingModels)) {
            CacheService::invalidateSitemap();
        }
        
        match ($class) {
            \App\Models\Country::class => CacheService::invalidateCountries(),
            \App\Models\ExamType::class => CacheService::invalidateExamTypes($model->country_id ?? null),
            \App\Models\Governorate::class => CacheService::invalidateGovernorates($model->country_id ?? null),
            \App\Models\ExamBranch::class => CacheService::invalidateBranches($model->exam_type_id ?? null),
            \App\Models\Setting::class => CacheService::invalidateSettings($model->key ?? null),
            \App\Models\SiteSetting::class => CacheService::invalidateSettings('site'),
            default => null,
        };
    }
}
