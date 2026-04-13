<?php

namespace App\Filament\Widgets;

use App\Models\Governorate;
use App\Models\Result;
use App\Models\ExamType;
use App\Models\UploadLog;
use App\Models\Country;
use App\Models\ResultSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;
    
    protected function getStats(): array
    {
        // Count active countries
        $activeCountries = Country::where('is_active', true)->count();
        
        // Count declared governorates
        $declaredGovernorates = Governorate::where('is_declared', true)->count();
        $totalGovernorates = Governorate::count();
        
        // Count results
        $totalResults = Result::count();
        
        // Count successful uploads today
        $todayUploads = UploadLog::whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();
        
        // Upcoming schedules count
        $upcomingSchedules = ResultSchedule::where('is_active', true)
            ->where('expected_date', '>', now())
            ->count();
        
        // Calculate success rate using pre-calculated status field
        $passedCount = Result::where('status', 'ناجح')->count();
        
        $successRate = $totalResults > 0 ? round(($passedCount / $totalResults) * 100, 1) : 0;

        return [
            Stat::make('إجمالي النتائج', number_format($totalResults))
                ->description('طالب مسجل في النظام')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-primary-50 transition-colors rounded-xl',
                ]),
            
            Stat::make('النتائج المعتمدة', "{$declaredGovernorates} / {$totalGovernorates}")
                ->description('محافظة تم اعتماد نتيجتها')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            
            Stat::make('ملفات اليوم', $todayUploads)
                ->description('ملف تم رفعه اليوم')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('warning'),
            
            Stat::make('مواعيد قادمة', $upcomingSchedules)
                ->description('موعد نتيجة منتظر')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
}
