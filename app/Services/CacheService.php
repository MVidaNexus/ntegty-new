<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Cache keys prefixes
     */
    const PREFIX_RESULTS = 'results:';
    const PREFIX_EXAM_TYPES = 'exam_types:';
    const PREFIX_COUNTRIES = 'countries:';
    const PREFIX_GOVERNORATES = 'governorates:';
    const PREFIX_BRANCHES = 'branches:';
    const PREFIX_SETTINGS = 'settings:';
    const PREFIX_STATS = 'stats:';
    const PREFIX_PAGES = 'pages:';
    const PREFIX_PAGE_CACHE = 'page_cache:';
    const PREFIX_SITEMAP = 'sitemap:';

    /**
     * Default TTL in seconds (1 hour)
     */
    const DEFAULT_TTL = 3600;

    /**
     * Long TTL for rarely changing data (24 hours)
     */
    const LONG_TTL = 86400;

    /**
     * Short TTL for frequently changing data (5 minutes)
     */
    const SHORT_TTL = 300;

    /**
     * Get cached result by seat number
     */
    public static function getResult(int $examTypeId, int $academicYearId, string $seatNumber, ?int $branchId = null, ?int $governorateId = null): ?array
    {
        $key = self::PREFIX_RESULTS . "{$examTypeId}:{$academicYearId}:{$seatNumber}";
        if ($branchId) {
            $key .= ":b{$branchId}";
        }
        if ($governorateId) {
            $key .= ":g{$governorateId}";
        }
        
        return Cache::get($key);
    }

    /**
     * Cache a result - TTL 24 hours for student results
     */
    public static function setResult(int $examTypeId, int $academicYearId, string $seatNumber, array $data, ?int $branchId = null, ?int $governorateId = null): void
    {
        $key = self::PREFIX_RESULTS . "{$examTypeId}:{$academicYearId}:{$seatNumber}";
        if ($branchId) {
            $key .= ":b{$branchId}";
        }
        if ($governorateId) {
            $key .= ":g{$governorateId}";
        }
        
        // Cache for 24 hours - results don't change frequently
        Cache::put($key, $data, self::LONG_TTL);
    }

    /**
     * Get all exam types (cached)
     */
    public static function getExamTypes(?int $countryId = null): mixed
    {
        $key = self::PREFIX_EXAM_TYPES . ($countryId ?? 'all');
        
        return Cache::remember($key, self::LONG_TTL, function () use ($countryId) {
            $query = \App\Models\ExamType::query();
            if ($countryId) {
                $query->where('country_id', $countryId);
            }
            return $query->with('branches')->get();
        });
    }

    /**
     * Get all countries (cached)
     */
    public static function getCountries(): mixed
    {
        return Cache::remember(self::PREFIX_COUNTRIES . 'all', self::LONG_TTL, function () {
            return \App\Models\Country::where('is_active', true)->orderBy('id')->get();
        });
    }

    /**
     * Get governorates by country (cached)
     */
    public static function getGovernorates(int $countryId): mixed
    {
        $key = self::PREFIX_GOVERNORATES . $countryId;
        
        return Cache::remember($key, self::LONG_TTL, function () use ($countryId) {
            return \App\Models\Governorate::where('country_id', $countryId)
                ->orderBy('sort_order')
                ->orderBy('name_ar')
                ->get();
        });
    }

    /**
     * Get branches by exam type (cached)
     */
    public static function getBranches(int $examTypeId): mixed
    {
        $key = self::PREFIX_BRANCHES . $examTypeId;
        
        return Cache::remember($key, self::LONG_TTL, function () use ($examTypeId) {
            return \App\Models\ExamBranch::where('exam_type_id', $examTypeId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Get setting value (cached)
     */
    public static function getSetting(string $key, $default = null): mixed
    {
        $cacheKey = self::PREFIX_SETTINGS . $key;
        
        return Cache::remember($cacheKey, self::LONG_TTL, function () use ($key, $default) {
            return \App\Models\Setting::where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Get site settings (cached)
     */
    public static function getSiteSettings(): mixed
    {
        return Cache::remember(self::PREFIX_SETTINGS . 'site', self::LONG_TTL, function () {
            return \App\Models\SiteSetting::first();
        });
    }

    /**
     * Get statistics (cached)
     */
    public static function getStats(): array
    {
        return Cache::remember(self::PREFIX_STATS . 'global', self::SHORT_TTL, function () {
            return [
                'total_results' => \App\Models\Result::count(),
                'total_countries' => \App\Models\Country::where('is_active', true)->count(),
                'total_exam_types' => \App\Models\ExamType::count(),
                'total_uploads' => \App\Models\UploadLog::where('status', 'completed')->count(),
                'today_searches' => \App\Models\PageView::whereDate('created_at', today())->count(),
            ];
        });
    }

    /**
     * Get results count by exam type (cached)
     */
    public static function getResultsCount(int $examTypeId, ?int $academicYearId = null): int
    {
        $key = self::PREFIX_STATS . "results_count:{$examTypeId}";
        if ($academicYearId) {
            $key .= ":{$academicYearId}";
        }
        
        return Cache::remember($key, self::SHORT_TTL, function () use ($examTypeId, $academicYearId) {
            $query = \App\Models\Result::where('exam_type_id', $examTypeId);
            if ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }
            return $query->count();
        });
    }

    /**
     * Cache a page/view
     */
    public static function cachePage(string $key, callable $callback, int $ttl = null): mixed
    {
        return Cache::remember(self::PREFIX_PAGES . $key, $ttl ?? self::DEFAULT_TTL, $callback);
    }

    /**
     * Invalidate results cache for an exam type
     */
    public static function invalidateResults(int $examTypeId, ?int $academicYearId = null): void
    {
        $pattern = self::PREFIX_RESULTS . "{$examTypeId}:*";
        self::deleteByPattern($pattern);
        
        // Also invalidate stats
        self::invalidateStats();
    }

    /**
     * Invalidate exam types cache
     */
    public static function invalidateExamTypes(?int $countryId = null): void
    {
        if ($countryId) {
            Cache::forget(self::PREFIX_EXAM_TYPES . $countryId);
        }
        Cache::forget(self::PREFIX_EXAM_TYPES . 'all');
    }

    /**
     * Invalidate countries cache
     */
    public static function invalidateCountries(): void
    {
        Cache::forget(self::PREFIX_COUNTRIES . 'all');
    }

    /**
     * Invalidate governorates cache
     */
    public static function invalidateGovernorates(?int $countryId = null): void
    {
        if ($countryId) {
            Cache::forget(self::PREFIX_GOVERNORATES . $countryId);
        } else {
            self::deleteByPattern(self::PREFIX_GOVERNORATES . '*');
        }
    }

    /**
     * Invalidate branches cache
     */
    public static function invalidateBranches(?int $examTypeId = null): void
    {
        if ($examTypeId) {
            Cache::forget(self::PREFIX_BRANCHES . $examTypeId);
        } else {
            self::deleteByPattern(self::PREFIX_BRANCHES . '*');
        }
    }

    /**
     * Invalidate settings cache
     */
    public static function invalidateSettings(?string $key = null): void
    {
        Cache::forget('all_settings');
        Cache::forget('all_site_settings');
        
        if ($key) {
            Cache::forget(self::PREFIX_SETTINGS . $key);
        } else {
            self::deleteByPattern(self::PREFIX_SETTINGS . '*');
        }
    }

    /**
     * Invalidate stats cache
     */
    public static function invalidateStats(): void
    {
        self::deleteByPattern(self::PREFIX_STATS . '*');
    }

    /**
     * Invalidate all pages cache
     */
    public static function invalidatePages(): void
    {
        self::deleteByPattern(self::PREFIX_PAGES . '*');
    }

    /**
     * Invalidate sitemap cache
     */
    public static function invalidateSitemap(): void
    {
        self::deleteByPattern(self::PREFIX_SITEMAP . '*');
    }

    /**
     * Invalidate all page cache (middleware-level cache)
     */
    public static function invalidatePageCache(): void
    {
        self::deleteByPattern(self::PREFIX_PAGE_CACHE . '*');
    }

    /**
     * Clear all application cache
     */
    public static function clearAll(): array
    {
        $stats = self::getCacheStats();
        
        Cache::flush();
        
        return [
            'success' => true,
            'cleared_keys' => $stats['total_keys'] ?? 0,
            'message' => 'تم مسح جميع الكاش بنجاح',
        ];
    }

    /**
     * Delete cache by pattern (Redis only)
     */
    public static function deleteByPattern(string $pattern): int
    {
        $deleted = 0;
        
        if (config('cache.default') !== 'redis') {
            return 0;
        }

        try {
            $fullPrefix = self::getCachePrefix();
            $fullPattern = $fullPrefix . $pattern;
            
            $cacheConnection = Redis::connection('cache');
            // Use command() to bypass auto-prefix
            $keys = $cacheConnection->command('keys', [$fullPattern]);
            
            if (!empty($keys)) {
                foreach ($keys as $key) {
                    // Get clean key without full prefix for Cache::forget
                    $cleanKey = str_replace($fullPrefix, '', $key);
                    Cache::forget($cleanKey);
                    $deleted++;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Cache pattern delete failed: ' . $e->getMessage());
        }
        
        return $deleted;
    }

    /**
     * Get the full cache prefix used by Laravel
     * Note: When using Redis command(), the database prefix is auto-added,
     * so we only need the cache prefix part
     */
    private static function getCachePrefix(): string
    {
        return config('cache.prefix', '') . ':';
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        $stats = [
            'driver' => config('cache.default'),
            'total_keys' => 0,
            'memory_used' => 'N/A',
            'memory_peak' => 'N/A',
            'uptime' => 'N/A',
            'hits' => 0,
            'misses' => 0,
            'hit_rate' => '0%',
            'categories' => [],
        ];

        try {
            if (config('cache.default') === 'redis') {
                $info = Redis::info();
                
                $stats['memory_used'] = self::formatBytes($info['used_memory'] ?? 0);
                $stats['memory_peak'] = self::formatBytes($info['used_memory_peak'] ?? 0);
                $stats['uptime'] = self::formatUptime($info['uptime_in_seconds'] ?? 0);
                $stats['hits'] = $info['keyspace_hits'] ?? 0;
                $stats['misses'] = $info['keyspace_misses'] ?? 0;
                
                $totalRequests = $stats['hits'] + $stats['misses'];
                $stats['hit_rate'] = $totalRequests > 0 
                    ? round(($stats['hits'] / $totalRequests) * 100, 2) . '%' 
                    : '0%';
                
                // Get full cache prefix
                $fullPrefix = self::getCachePrefix();
                
                $categories = [
                    'results' => self::PREFIX_RESULTS,
                    'exam_types' => self::PREFIX_EXAM_TYPES,
                    'countries' => self::PREFIX_COUNTRIES,
                    'governorates' => self::PREFIX_GOVERNORATES,
                    'branches' => self::PREFIX_BRANCHES,
                    'settings' => self::PREFIX_SETTINGS,
                    'stats' => self::PREFIX_STATS,
                    'pages' => self::PREFIX_PAGES,
                    'page_cache' => self::PREFIX_PAGE_CACHE,
                ];
                
                // Use cache connection with command() to bypass auto-prefix
                $cacheConnection = Redis::connection('cache');
                
                foreach ($categories as $name => $catPrefix) {
                    $keys = $cacheConnection->command('keys', [$fullPrefix . $catPrefix . '*']);
                    $stats['categories'][$name] = count($keys);
                    $stats['total_keys'] += count($keys);
                }
                
                // Get other keys
                $allKeys = $cacheConnection->command('keys', [$fullPrefix . '*']);
                $stats['categories']['other'] = count($allKeys) - $stats['total_keys'];
                $stats['total_keys'] = count($allKeys);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get cache stats: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get cached keys list
     */
    public static function getCachedKeys(string $category = null, int $limit = 100): array
    {
        $keys = [];
        
        try {
            $cachePrefix = self::getCachePrefix();
            $dbPrefix = config('database.redis.options.prefix', '');
            $fullPrefix = $dbPrefix . $cachePrefix;
            
            $pattern = $cachePrefix;
            
            if ($category) {
                $categoryPrefixes = [
                    'results' => self::PREFIX_RESULTS,
                    'exam_types' => self::PREFIX_EXAM_TYPES,
                    'countries' => self::PREFIX_COUNTRIES,
                    'governorates' => self::PREFIX_GOVERNORATES,
                    'branches' => self::PREFIX_BRANCHES,
                    'settings' => self::PREFIX_SETTINGS,
                    'stats' => self::PREFIX_STATS,
                    'pages' => self::PREFIX_PAGES,
                ];
                
                $pattern .= $categoryPrefixes[$category] ?? '';
            }
            
            $pattern .= '*';
            $cacheConnection = Redis::connection('cache');
            // command('keys') returns full key names with db prefix
            $redisKeys = $cacheConnection->command('keys', [$pattern]);
            
            foreach (array_slice($redisKeys, 0, $limit) as $fullKey) {
                // Clean key for display (remove full prefix)
                $cleanKey = str_replace($fullPrefix, '', $fullKey);
                // For ttl, use key without db prefix (since command auto-adds it)
                $keyForTtl = str_replace($dbPrefix, '', $fullKey);
                $ttl = $cacheConnection->command('ttl', [$keyForTtl]);
                
                $keys[] = [
                    'key' => $cleanKey,
                    'ttl' => $ttl > 0 ? self::formatTTL($ttl) : ($ttl == -1 ? 'لا ينتهي' : 'منتهي'),
                    'ttl_seconds' => $ttl,
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get cached keys: ' . $e->getMessage());
        }

        return $keys;
    }

    /**
     * Warm up cache with essential data
     */
    public static function warmUp(): array
    {
        $warmed = [];
        
        try {
            // Cache countries
            self::getCountries();
            $warmed[] = 'countries';
            
            // Cache exam types
            self::getExamTypes();
            $warmed[] = 'exam_types';
            
            // Cache site settings
            self::getSiteSettings();
            $warmed[] = 'site_settings';
            
            // Cache stats
            self::getStats();
            $warmed[] = 'stats';
            
            // Cache governorates for Egypt
            $egypt = \App\Models\Country::where('code', 'EG')->first();
            if ($egypt) {
                self::getGovernorates($egypt->id);
                $warmed[] = 'egypt_governorates';
            }
            
            // Cache branches for main exam types
            $examTypes = \App\Models\ExamType::whereHas('branches')->get();
            foreach ($examTypes as $examType) {
                self::getBranches($examType->id);
                $warmed[] = "branches_{$examType->code}";
            }
            
        } catch (\Exception $e) {
            Log::error('Cache warm-up failed: ' . $e->getMessage());
        }

        return $warmed;
    }

    /**
     * Format bytes to human readable
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Format uptime to human readable
     */
    private static function formatUptime(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        $parts = [];
        if ($days > 0) $parts[] = "{$days} يوم";
        if ($hours > 0) $parts[] = "{$hours} ساعة";
        if ($minutes > 0) $parts[] = "{$minutes} دقيقة";
        
        return implode(' و ', $parts) ?: '< دقيقة';
    }

    /**
     * Format TTL to human readable
     */
    private static function formatTTL(int $seconds): string
    {
        if ($seconds < 60) return "{$seconds} ثانية";
        if ($seconds < 3600) return floor($seconds / 60) . ' دقيقة';
        if ($seconds < 86400) return floor($seconds / 3600) . ' ساعة';
        return floor($seconds / 86400) . ' يوم';
    }
}
