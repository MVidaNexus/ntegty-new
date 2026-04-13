<?php

namespace App\Filament\Widgets;

use App\Models\ResultSchedule;
use App\Models\Governorate;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class UpcomingResultsWidget extends Widget
{
    protected static string $view = 'filament.widgets.upcoming-results';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 1;

    public function getUpcomingResults(): array
    {
        $schedules = ResultSchedule::with(['country', 'examType', 'governorate'])
            ->where('is_active', true)
            ->where('expected_date', '>', now())
            ->orderBy('expected_date', 'asc')
            ->limit(10)
            ->get();

        return $schedules->map(function ($schedule) {
            $now = Carbon::now();
            $target = $schedule->expected_date;
            $diff = $now->diff($target);
            
            $timeRemaining = '';
            if ($diff->days > 0) {
                $timeRemaining = $diff->days . ' يوم';
                if ($diff->h > 0) {
                    $timeRemaining .= ' و ' . $diff->h . ' ساعة';
                }
            } elseif ($diff->h > 0) {
                $timeRemaining = $diff->h . ' ساعة';
                if ($diff->i > 0) {
                    $timeRemaining .= ' و ' . $diff->i . ' دقيقة';
                }
            } else {
                $timeRemaining = $diff->i . ' دقيقة';
            }

            $name = $schedule->examType?->name_ar ?? 'نتيجة عامة';
            if ($schedule->governorate) {
                $name .= ' - ' . $schedule->governorate->name_ar;
            } elseif ($schedule->country) {
                $name .= ' - ' . $schedule->country->name_ar;
            }

            return [
                'id' => $schedule->id,
                'name' => $name,
                'date' => $target->format('Y/m/d'),
                'time' => $target->format('h:i A'),
                'remaining' => $timeRemaining,
                'days' => $diff->days,
                'note' => $schedule->note,
                'is_soon' => $diff->days < 3,
            ];
        })->toArray();
    }

    public function getDeclaredResults(): array
    {
        $declared = Governorate::with('country')
            ->where('is_declared', true)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return $declared->map(function ($gov) {
            return [
                'id' => $gov->id,
                'name' => $gov->name_ar,
                'country' => $gov->country?->name_ar,
                'declared_at' => $gov->updated_at->diffForHumans(),
            ];
        })->toArray();
    }
}
