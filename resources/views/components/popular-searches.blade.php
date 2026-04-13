@props([
    'examType' => null,
    'governorate' => null,
    'country' => null,
])

@php
    // التحقق من تفعيل كلمات البحث الشائعة
    $showPopularSearches = $examType ? ($examType->show_popular_searches ?? true) : true;
    
    if (!$showPopularSearches) {
        return;
    }
    
    $searches = [];
    
    // جمع الكلمات من ExamType
    if ($examType && !empty($examType->popular_searches)) {
        $searches = array_merge($searches, $examType->popular_searches);
    }
    
    // إذا لم تكن هناك كلمات محددة، نولد كلمات افتراضية
    if (empty($searches)) {
        $examName = $examType->name_ar ?? 'النتيجة';
        $countryName = $country->name_ar ?? ($examType->country->name_ar ?? 'مصر');
        $govName = $governorate->name_ar ?? null;
        $year = date('Y');
        
        if ($govName) {
            $searches = [
                "نتيجة {$examName} محافظة {$govName}",
                "نتيجة {$govName} {$year}",
                "{$examName} {$govName} بالاسم",
                "نتيجة {$govName} برقم الجلوس",
            ];
        } else {
            $searches = [
                "نتيجة {$examName} {$year}",
                "{$examName} بالاسم ورقم الجلوس",
                "نتيجة {$examName} {$countryName}",
                "موعد ظهور نتيجة {$examName}",
            ];
        }
    }
    
    // أخذ أول 4 كلمات فقط
    $searches = array_slice($searches, 0, 4);
@endphp

@if($showPopularSearches && count($searches) > 0)
<div class="w-full max-w-6xl mx-auto mt-8 px-3 no-print">
    <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center gap-2 mb-4">
            <i class="fa-solid fa-magnifying-glass-chart text-blue-500 text-lg"></i>
            <span class="text-base font-bold text-gray-700">كلمات بحث شائعة:</span>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($searches as $search)
            <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-blue-50 text-gray-700 text-sm rounded-full cursor-default transition-colors border border-gray-200 shadow-sm">
                <i class="fa-solid fa-search text-xs text-blue-400"></i>
                {{ $search }}
            </span>
            @endforeach
        </div>
    </div>
</div>
@endif
