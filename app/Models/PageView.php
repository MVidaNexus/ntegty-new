<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;

class PageView extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'page_type',
        'governorate_id',
        'ip_address',
        'user_agent',
        'referer',
        'country_code',
        'device_type',
        'browser',
    ];

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    /**
     * Record a page view
     */
    public static function record(string $path, ?string $pageType = null, ?int $governorateId = null): void
    {
        $request = request();
        $userAgent = $request->userAgent();
        
        // Detect device type and browser
        $deviceType = 'desktop';
        $browser = 'Unknown';
        
        if (preg_match('/mobile|android|iphone|ipad/i', $userAgent)) {
            $deviceType = preg_match('/ipad|tablet/i', $userAgent) ? 'tablet' : 'mobile';
        }
        
        if (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = 'Opera';
        }

        self::create([
            'path' => substr($path, 0, 255),
            'page_type' => $pageType,
            'governorate_id' => $governorateId,
            'ip_address' => $request->ip(),
            'user_agent' => substr($userAgent ?? '', 0, 255),
            'referer' => substr($request->header('referer') ?? '', 0, 255),
            'device_type' => $deviceType,
            'browser' => $browser,
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public static function getStats(int $days = 7): array
    {
        $startDate = now()->subDays($days);
        
        return [
            'total_views' => self::where('created_at', '>=', $startDate)->count(),
            'unique_visitors' => self::where('created_at', '>=', $startDate)
                ->distinct('ip_address')
                ->count('ip_address'),
            'today_views' => self::whereDate('created_at', today())->count(),
            'yesterday_views' => self::whereDate('created_at', today()->subDay())->count(),
            'by_device' => self::where('created_at', '>=', $startDate)
                ->selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->pluck('count', 'device_type')
                ->toArray(),
            'by_browser' => self::where('created_at', '>=', $startDate)
                ->selectRaw('browser, COUNT(*) as count')
                ->groupBy('browser')
                ->orderByDesc('count')
                ->limit(5)
                ->pluck('count', 'browser')
                ->toArray(),
            'by_page_type' => self::where('created_at', '>=', $startDate)
                ->whereNotNull('page_type')
                ->selectRaw('page_type, COUNT(*) as count')
                ->groupBy('page_type')
                ->pluck('count', 'page_type')
                ->toArray(),
            'popular_governorates' => self::where('created_at', '>=', $startDate)
                ->whereNotNull('governorate_id')
                ->selectRaw('governorate_id, COUNT(*) as count')
                ->groupBy('governorate_id')
                ->orderByDesc('count')
                ->limit(10)
                ->with('governorate:id,name_ar')
                ->get()
                ->map(fn($item) => [
                    'name' => $item->governorate?->name_ar ?? 'غير معروف',
                    'count' => $item->count,
                ])
                ->toArray(),
            'daily_views' => self::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray(),
        ];
    }
}
