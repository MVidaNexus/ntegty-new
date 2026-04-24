<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use App\Observers\ResultObserver;
use App\Observers\CacheInvalidationObserver;
use App\Services\CacheService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        // Models use $fillable for mass assignment protection

        // Register Observers for cache invalidation
        \App\Models\Result::observe(ResultObserver::class);
        \App\Models\Country::observe(CacheInvalidationObserver::class);
        \App\Models\ExamType::observe(CacheInvalidationObserver::class);
        \App\Models\Governorate::observe(CacheInvalidationObserver::class);
        \App\Models\ExamBranch::observe(CacheInvalidationObserver::class);
        \App\Models\Setting::observe(CacheInvalidationObserver::class);
        \App\Models\SiteSetting::observe(CacheInvalidationObserver::class);

        try {
            // Share active countries with all views (cached)
            if (\Illuminate\Support\Facades\Schema::hasTable('countries')) {
                $headerCountries = CacheService::getCountries();
                \Illuminate\Support\Facades\View::share('headerCountries', $headerCountries);
            }

            // Share settings with all views (cached)
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \Illuminate\Support\Facades\Cache::remember('all_settings', 3600, function () {
                    return \App\Models\Setting::all()->pluck('value', 'key')->toArray();
                });
                
                // Merge site settings (fb_app_id, og_image, etc.)
                if (\Illuminate\Support\Facades\Schema::hasTable('site_settings')) {
                    $siteSettings = \Illuminate\Support\Facades\Cache::remember('all_site_settings', 3600, function () {
                        return \App\Models\SiteSetting::all()->pluck('value', 'key')->toArray();
                    });
                    $settings = array_merge($settings, $siteSettings);
                }
                
                \Illuminate\Support\Facades\View::share('settings', $settings);
            }
        } catch (\Exception $e) {
            // Log error or ignore if DB not ready
        }
    }
}
