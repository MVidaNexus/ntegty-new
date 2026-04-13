@extends('layouts.layout')

@section('structured_data')
@if(isset($structuredData))
{!! $structuredData !!}
@endif
@endsection

@section('content')
<div class="w-full bg-gradient-to-b from-slate-50 to-white py-6 print:bg-white print:py-0">
    <div class="w-full px-3 max-w-[1400px] mx-auto print:max-w-none print:px-0">
        
        <!-- ==================== قسم الطباعة فقط ==================== -->
        <div class="print-only-section" style="display: none;">
            <!-- ترويسة الطباعة -->
            <div class="print-header">
                <table class="header-table">
                    <tr>
                        <td class="header-right">
                            <img src="https://flagcdn.com/w160/eg.png" class="flag-img" alt="علم مصر">
                            <div>
                                <div class="header-title">جمهورية مصر العربية</div>
                                <div class="header-subtitle">وزارة التربية والتعليم</div>
                            </div>
                        </td>
                        <td class="header-center">
                            <div class="logo-text">🎓 نتيجتي</div>
                            <div class="site-url">ntegty.com</div>
                        </td>
                        <td class="header-left">
                            <div class="year-box">
                                <div class="year-label">العام الدراسي</div>
                                <div class="year-value">{{ isset($academicYear) ? $academicYear->year : '' }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- عنوان الكشف -->
            <div class="print-title-section">
                <h1 class="print-main-title">
                    @if(isset($topCount) && $topCount != 'all')
                        🏆 كشف أوائل الثانوية العامة
                    @else
                        📋 كشف نتائج الثانوية العامة
                    @endif
                </h1>
                <div class="print-badges">
                    <span class="print-badge badge-system">
                        @if(isset($systemType) && $systemType)
                            {{ $systemType == 'old' ? '📚 النظام القديم' : '💻 النظام الحديث' }}
                        @else
                            📊 جميع الأنظمة
                        @endif
                    </span>
                    <span class="print-badge badge-branch">
                        @if(isset($selectedBranch))
                            🎓 {{ $selectedBranch->name_ar }}
                        @else
                            📖 جميع الشعب
                        @endif
                    </span>
                    @if(isset($topCount) && $topCount != 'all')
                        <span class="print-badge badge-count">👥 أعلى {{ $topCount }} طالب</span>
                    @endif
                </div>
            </div>
            
            <!-- إحصائيات سريعة -->
            <div class="print-stats">
                <div class="stat-item">
                    <span class="stat-label">إجمالي الطلاب</span>
                    <span class="stat-value">{{ number_format($totalStudents) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">أعلى مجموع</span>
                    <span class="stat-value">{{ $highestScore }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">المجموع الكلي</span>
                    <span class="stat-value">{{ isset($examTotalScore) ? number_format($examTotalScore) : '-' }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">تاريخ الطباعة</span>
                    <span class="stat-value">{{ now()->format('Y/m/d') }}</span>
                </div>
            </div>
            
            <!-- جدول النتائج للطباعة -->
            <table class="print-results-table">
                <thead>
                    <tr>
                        <th class="col-rank">م</th>
                        <th class="col-name">اسم الطالب</th>
                        <th class="col-seat">رقم الجلوس</th>
                        <th class="col-score">المجموع</th>
                        <th class="col-percent">النسبة %</th>
                        <th class="col-status">الحالة</th>
                        <th class="col-branch">الشعبة</th>
                        <th class="col-system">النظام</th>
                        <th class="col-gov">المحافظة</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $printItems = isset($isPaginated) && $isPaginated ? $results->items() : $results;
                        $printStartIndex = isset($isPaginated) && $isPaginated ? ($results->currentPage() - 1) * $results->perPage() : 0;
                    @endphp
                    @forelse($printItems as $index => $result)
                    @php
                        $printRank = $printStartIndex + $index + 1;
                        $printStatus = $result->calculated_status;
                        $percent = isset($examTotalScore) && $examTotalScore > 0 ? round(($result->total_score / $examTotalScore) * 100, 2) : 0;
                    @endphp
                    <tr class="{{ $printRank <= 3 ? 'top-three' : '' }} {{ $printRank % 2 == 0 ? 'even-row' : '' }}">
                        <td class="col-rank">
                            @if($printRank == 1)
                                🥇
                            @elseif($printRank == 2)
                                🥈
                            @elseif($printRank == 3)
                                🥉
                            @else
                                {{ $printRank }}
                            @endif
                        </td>
                        <td class="col-name">{{ $result->student_name }}</td>
                        <td class="col-seat">{{ $result->seat_number }}</td>
                        <td class="col-score">{{ $result->total_score }}</td>
                        <td class="col-percent">{{ $percent }}%</td>
                        <td class="col-status {{ $printStatus === 'ناجح' ? 'status-pass' : 'status-fail' }}">{{ $printStatus }}</td>
                        <td class="col-branch">{{ $result->branch ? $result->branch->name_ar : '-' }}</td>
                        <td class="col-system">{{ $result->system_type == 'old' ? 'قديم' : ($result->system_type == 'new' ? 'حديث' : '-') }}</td>
                        <td class="col-gov">{{ $result->governorate ? $result->governorate->name_ar : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="empty-row">لا توجد نتائج</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- تذييل الطباعة -->
            <div class="print-footer">
                <div class="footer-note">
                    ⚠️ تنبيه: هذا الكشف غير رسمي - للاستعلام الرسمي يرجى مراجعة المدرسة أو موقع الوزارة
                </div>
                <div class="footer-info">
                    <span>ntegty.com</span>
                    <span>•</span>
                    <span>نتيجتي - نتائج الطلاب</span>
                    <span>•</span>
                    <span>{{ now()->format('Y/m/d H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- ==================== الشاشة العادية ==================== -->
        <div class="screen-content">
        <!-- Breadcrumbs -->
        @if(isset($breadcrumbs))
        <nav class="mb-4 text-sm no-print">
            <ol class="flex items-center gap-2 text-gray-600">
                @foreach($breadcrumbs as $index => $crumb)
                    @if($index > 0)
                        <li><i class="fa-solid fa-chevron-left text-xs mx-1"></i></li>
                    @endif
                    <li>
                        @if(isset($crumb['url']))
                            <a href="{{ $crumb['url'] }}" class="hover:text-emerald-600 transition-colors">{{ $crumb['name'] }}</a>
                        @else
                            <span class="text-gray-800 font-semibold">{{ $crumb['name'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
        @endif

        <!-- Page Header with Trophy -->
        <div class="text-center mb-8 no-print">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-full mb-4 shadow-lg">
                <i class="fa-solid fa-trophy text-4xl text-white"></i>
            </div>
            <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-2 leading-tight">
                {{ $title }}
            </h1>
            <p class="text-lg md:text-xl font-bold text-emerald-600 mb-1">
                @if(isset($selectedBranch))
                    {{ $selectedBranch->name_ar }}
                @else
                    جميع الشعب
                @endif
                @if(isset($systemType) && $systemType)
                    - {{ $systemType == 'old' ? 'نظام قديم' : 'نظام حديث' }}
                @endif
            </p>
            <p class="text-sm text-gray-500">
                <i class="fa-solid fa-calendar-alt ml-1"></i>
                {{ isset($academicYear) ? $academicYear->year : '' }}
            </p>
        </div>

        <!-- زر الطباعة الرئيسي -->
        <div class="flex justify-center mb-6 no-print">
            <button type="button" onclick="window.print()" 
                    class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105 cursor-pointer text-lg">
                <i class="fa-solid fa-print text-xl"></i>
                <span>طباعة كشف الأوائل</span>
            </button>
        </div>

        <!-- Quick Stats -->
        @php
            $resultItems = isset($isPaginated) && $isPaginated ? $results->items() : $results;
            $displayedCount = isset($isPaginated) && $isPaginated ? $results->total() : count($results);
            
            // Get total score based on system type from ExamType settings
            $systemSettings = isset($examType) ? $examType->getSettingsForSystemType($systemType ?? null) : null;
            $examTotalScore = $systemSettings['total_score'] ?? $examType->total_score ?? null;
        @endphp
        
        <!-- المجموع الكلي للامتحان -->
        @if($examTotalScore)
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl p-4 mb-6 max-w-md mx-auto text-center shadow-lg no-print">
            <p class="text-white/80 text-sm font-medium mb-1">
                المجموع الكلي للامتحان
                @if(isset($systemType) && $systemType)
                    ({{ $systemType == 'old' ? 'النظام القديم' : 'النظام الحديث' }})
                @endif
            </p>
            <p class="text-4xl font-black text-white">{{ number_format($examTotalScore) }}</p>
            <p class="text-white/70 text-xs mt-1">درجة</p>
        </div>
        @endif
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 max-w-3xl mx-auto no-print">
            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-4 border-2 border-amber-200 text-center">
                <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fa-solid fa-users text-amber-600 text-xl"></i>
                </div>
                <p class="text-sm text-amber-700 font-medium">إجمالي الطلاب</p>
                <p class="text-2xl font-black text-amber-800">{{ number_format($totalStudents) }}</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl p-4 border-2 border-emerald-200 text-center">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fa-solid fa-trophy text-emerald-600 text-xl"></i>
                </div>
                <p class="text-sm text-emerald-700 font-medium">أعلى مجموع</p>
                <p class="text-2xl font-black text-emerald-800">{{ $highestScore }}</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border-2 border-blue-200 text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fa-solid fa-star text-blue-600 text-xl"></i>
                </div>
                <p class="text-sm text-blue-700 font-medium">المعروض</p>
                <p class="text-2xl font-black text-blue-800">{{ $displayedCount }}</p>
            </div>
            <!-- زر الطباعة -->
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-4 border-2 border-indigo-200 text-center flex flex-col items-center justify-center">
                <button type="button" onclick="window.print()" 
                        class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-2 hover:bg-indigo-200 transition cursor-pointer">
                    <i class="fa-solid fa-print text-indigo-600 text-xl"></i>
                </button>
                <p class="text-sm text-indigo-700 font-medium">طباعة الأوائل</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-4 mb-6 no-print">
            <form method="GET" action="{{ route('egypt.secondary.all-results') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Academic Year Filter -->
                @if(isset($showYearFilter) && $showYearFilter && isset($academicYears) && $academicYears->count() > 1)
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-calendar text-emerald-500 ml-1"></i>
                        السنة الدراسية
                    </label>
                    <select name="academic_year_id" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:outline-none transition-colors">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ isset($academicYear) && $academicYear->id == $year->id ? 'selected' : '' }}>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @elseif(isset($academicYear))
                    <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
                @endif
                
                <!-- Branch Filter -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-graduation-cap text-indigo-500 ml-1"></i>
                        الشعبة
                    </label>
                    <select name="branch_id" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors">
                        <option value="">كل الشعب</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name_ar }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- System Type Filter -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-cog text-amber-500 ml-1"></i>
                        نظام الدراسة
                    </label>
                    <select name="system_type" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:outline-none transition-colors">
                        <option value="">كل الأنظمة</option>
                        <option value="old" {{ (isset($systemType) && $systemType == 'old') ? 'selected' : '' }}>نظام قديم</option>
                        <option value="new" {{ (isset($systemType) && $systemType == 'new') ? 'selected' : '' }}>نظام حديث</option>
                    </select>
                </div>
                
                <!-- Top Count Filter -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-list-ol text-blue-500 ml-1"></i>
                        عدد الأوائل
                    </label>
                    <select name="top_count" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-colors">
                        <option value="10" {{ (!isset($topCount) || $topCount == '10') ? 'selected' : '' }}>أول 10</option>
                        <option value="50" {{ (isset($topCount) && $topCount == '50') ? 'selected' : '' }}>أول 50</option>
                        <option value="100" {{ (isset($topCount) && $topCount == '100') ? 'selected' : '' }}>أول 100</option>
                        <option value="500" {{ (isset($topCount) && $topCount == '500') ? 'selected' : '' }}>أول 500</option>
                        <option value="all" {{ (isset($topCount) && $topCount == 'all') ? 'selected' : '' }}>كل النتائج</option>
                    </select>
                </div>
                
                <!-- Search -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-search text-gray-500 ml-1"></i>
                        بحث
                    </label>
                    <div class="flex gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="الاسم أو رقم الجلوس"
                               class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-colors">
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold rounded-xl transition-all shadow-md">
                            <i class="fa-solid fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @php
            $isFullList = isset($topCount) && $topCount == 'all';
            $startIndex = isset($isPaginated) && $isPaginated ? ($results->currentPage() - 1) * $results->perPage() : 0;
        @endphp

        @if($isFullList)
        {{-- ==================== عرض الكشف الكامل - جدول ==================== --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                            <th class="px-3 py-4 text-center font-bold w-16">#</th>
                            <th class="px-4 py-4 text-right font-bold">اسم الطالب</th>
                            <th class="px-3 py-4 text-center font-bold">رقم الجلوس</th>
                            <th class="px-3 py-4 text-center font-bold">المجموع</th>
                            <th class="px-3 py-4 text-center font-bold">الحالة</th>
                            <th class="px-3 py-4 text-center font-bold no-print">الشعبة</th>
                            <th class="px-3 py-4 text-center font-bold no-print">النظام</th>
                            <th class="px-3 py-4 text-center font-bold no-print">المحافظة</th>
                            <th class="px-3 py-4 text-center font-bold no-print">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($resultItems as $index => $result)
                        @php
                            $rankNum = $startIndex + $index + 1;
                            $calcStatus = $result->calculated_status;
                        @endphp
                        <tr class="hover:bg-blue-50 transition-colors {{ $rankNum <= 3 ? 'bg-amber-50' : '' }}">
                            <td class="px-3 py-3 text-center">
                                @if($rankNum <= 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $rankNum == 1 ? 'bg-yellow-400' : ($rankNum == 2 ? 'bg-gray-300' : 'bg-amber-500') }} text-white font-black text-sm">
                                        {{ $rankNum }}
                                    </span>
                                @else
                                    <span class="text-gray-600 font-bold">{{ $rankNum }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('egypt.secondary.student', $result->seat_number) }}{{ isset($academicYear) && $academicYear ? '?academic_year_id=' . $academicYear->id : '' }}" 
                                   class="font-bold text-gray-800 hover:text-blue-600 transition-colors">
                                    {{ $result->student_name }}
                                </a>
                            </td>
                            <td class="px-3 py-3 text-center font-mono font-bold text-gray-700">{{ $result->seat_number }}</td>
                            <td class="px-3 py-3 text-center">
                                <span class="font-black text-lg {{ $calcStatus === 'ناجح' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $result->total_score }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="inline-block px-2 py-1 rounded-lg text-xs font-bold {{ $calcStatus === 'ناجح' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $calcStatus }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center no-print">
                                @if($result->branch)
                                    <span class="text-xs font-medium text-indigo-600">{{ $result->branch->name_ar }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center no-print">
                                @if($result->system_type)
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-bold {{ $result->system_type == 'old' ? 'bg-amber-100 text-amber-700' : 'bg-teal-100 text-teal-700' }}">
                                        {{ $result->system_type == 'old' ? 'قديم' : 'حديث' }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center no-print">
                                @if($result->governorate)
                                    <span class="text-xs text-gray-600">{{ $result->governorate->name_ar }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center no-print">
                                <a href="{{ route('egypt.secondary.student', $result->seat_number) }}{{ isset($academicYear) && $academicYear ? '?academic_year_id=' . $academicYear->id : '' }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold rounded-lg transition text-xs">
                                    <i class="fa-solid fa-eye"></i>
                                    عرض
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                                لا توجد نتائج
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @else
        {{-- ==================== عرض الأوائل - كروت ==================== --}}
        <!-- Top Students Cards -->
        <div class="space-y-4 print:space-y-2">
            @php
                $medalColors = [
                    1 => 'from-amber-400 to-yellow-500',
                    2 => 'from-slate-300 to-gray-400',
                    3 => 'from-amber-600 to-orange-700',
                ];
                $borderColors = [
                    1 => 'border-amber-400',
                    2 => 'border-slate-400',
                    3 => 'border-amber-600',
                ];
            @endphp
            
            @forelse($resultItems as $index => $result)
            @php
                $rankNum = $startIndex + $index + 1;
                $isTopThree = $rankNum <= 3;
                $gradientClass = $medalColors[$rankNum] ?? 'from-blue-500 to-indigo-600';
                $borderClass = $borderColors[$rankNum] ?? 'border-blue-500';
                $calcStatus = $result->calculated_status;
            @endphp
            <div class="bg-white rounded-2xl shadow-lg border-2 {{ $isTopThree ? $borderClass : 'border-gray-200' }} overflow-hidden transition-all hover:shadow-xl print:rounded-lg print:shadow-none print:border" x-data="{ showDetails: false }">
                <!-- Card Header -->
                <div class="bg-gradient-to-r {{ $gradientClass }} p-4 text-white print:p-2 print:bg-gray-800">
                    <div class="flex items-center justify-between flex-wrap gap-3 print:gap-2">
                        <div class="flex items-center gap-4 print:gap-2">
                            <!-- Rank Badge -->
                            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm flex-shrink-0 print:w-10 print:h-10">
                                @if($rankNum == 1)
                                    <i class="fa-solid fa-medal text-3xl sm:text-4xl text-yellow-300 print:text-xl"></i>
                                @elseif($rankNum == 2)
                                    <i class="fa-solid fa-medal text-3xl sm:text-4xl text-gray-200 print:text-xl"></i>
                                @elseif($rankNum == 3)
                                    <i class="fa-solid fa-medal text-3xl sm:text-4xl text-amber-400 print:text-xl"></i>
                                @else
                                    <span class="text-xl sm:text-2xl font-black print:text-lg">{{ $rankNum }}</span>
                                @endif
                            </div>
                            <!-- Student Info -->
                            <div>
                                <h3 class="text-lg sm:text-xl font-black">{{ $result->student_name }}</h3>
                                <p class="text-xs sm:text-sm opacity-90">
                                    <i class="fa-solid fa-id-card ml-1"></i>
                                    رقم الجلوس: {{ $result->seat_number }}
                                </p>
                                @if($result->branch)
                                <p class="text-xs opacity-80 mt-1">
                                    <i class="fa-solid fa-graduation-cap ml-1"></i>
                                    {{ $result->branch->name_ar }}
                                </p>
                                @endif
                                @if($result->governorate)
                                <p class="text-xs opacity-80">
                                    <i class="fa-solid fa-map-marker-alt ml-1"></i>
                                    {{ $result->governorate->name_ar }}
                                </p>
                                @endif
                            </div>
                        </div>
                        <!-- Total Score & Status -->
                        <div class="text-center sm:text-left">
                            <p class="text-xs sm:text-sm opacity-90">المجموع</p>
                            <p class="text-2xl sm:text-3xl font-black">{{ $result->total_score }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-bold
                                {{ $calcStatus === 'ناجح' ? 'bg-white/30 text-white' : ($calcStatus === 'راسب' ? 'bg-red-100 text-red-700' : 'bg-white/30 text-white') }}">
                                {{ $calcStatus }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Card Body -->
                <div class="p-4 print:p-2">
                    <!-- Info Row - hidden in print -->
                    <div class="flex flex-wrap justify-center gap-3 mb-3 no-print">
                        @if($result->system_type)
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                            {{ $result->system_type == 'old' ? 'bg-amber-100 text-amber-700' : 'bg-teal-100 text-teal-700' }}">
                            <i class="fa-solid fa-cog ml-1"></i>
                            {{ $result->system_type == 'old' ? 'نظام قديم' : 'نظام حديث' }}
                        </span>
                        @endif
                        @if($result->branch)
                        <span class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold">
                            <i class="fa-solid fa-graduation-cap ml-1"></i>
                            {{ $result->branch->name_ar }}
                        </span>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap justify-center gap-2">
                        <a href="{{ route('egypt.secondary.student', $result->seat_number) }}{{ isset($academicYear) && $academicYear ? '?academic_year_id=' . $academicYear->id : '' }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 font-bold rounded-lg transition text-sm">
                            <i class="fa-solid fa-user"></i>
                            عرض النتيجة الكاملة
                        </a>
                        @if($result->branch)
                        <a href="{{ route('egypt.secondary.branch', $result->branch->code) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-bold rounded-lg transition text-sm">
                            <i class="fa-solid fa-graduation-cap"></i>
                            {{ $result->branch->name_ar }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-200 p-12 text-center">
                <i class="fa-solid fa-inbox text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">لا توجد نتائج</p>
            </div>
            @endforelse
        </div>
        @endif
        {{-- نهاية الـ if/else للعرض --}}

        <!-- Pagination -->
        @if(isset($isPaginated) && $isPaginated)
        <div class="mt-6 no-print">
            {{ $results->withQueryString()->links() }}
        </div>
        @endif
        
        <!-- أزرار الطباعة والتصفح في النهاية -->
        <div class="mt-8 mb-6 p-6 bg-gradient-to-br from-slate-50 to-blue-50 rounded-2xl border-2 border-slate-200 no-print">
            <h3 class="text-lg font-black text-gray-800 mb-4 text-center">
                <i class="fa-solid fa-print text-blue-600 ml-2"></i>
                خيارات الطباعة والتصفح
            </h3>
            <div class="flex flex-wrap justify-center gap-4">
                <!-- زر طباعة الأوائل -->
                <a href="{{ route('egypt.secondary.all-results', array_merge(request()->all(), ['top_count' => 100])) }}" 
                   onclick="event.preventDefault(); window.location.href=this.href; setTimeout(() => window.print(), 500);"
                   class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-yellow-400 to-amber-500 hover:from-yellow-500 hover:to-amber-600 text-amber-900 font-bold rounded-xl transition-all shadow-lg hover:shadow-xl text-base">
                    <i class="fa-solid fa-trophy text-xl"></i>
                    <div class="text-right">
                        <span class="block">🏆 طباعة أوائل الثانوية العامة</span>
                        <span class="block text-xs opacity-80 mt-1">
                            ({{ isset($systemType) && $systemType ? ($systemType == 'old' ? 'النظام القديم' : 'النظام الحديث') : 'جميع الأنظمة' }} - أول 100 طالب)
                        </span>
                    </div>
                </a>
                
                <!-- زر طباعة الكشف الكامل -->
                <button type="button" onclick="window.print()" 
                        class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl text-base cursor-pointer">
                    <i class="fa-solid fa-print text-xl"></i>
                    <div class="text-right">
                        <span class="block">📋 طباعة هذه الصفحة</span>
                        <span class="block text-xs opacity-80 mt-1">
                            ({{ isset($systemType) && $systemType ? ($systemType == 'old' ? 'النظام القديم' : 'النظام الحديث') : 'جميع الأنظمة' }} - 
                            @if(isset($topCount) && $topCount == 'all')
                                كل النتائج
                            @else
                                أول {{ $topCount ?? 100 }} طالب
                            @endif
                            )
                        </span>
                    </div>
                </button>
                
                <!-- زر عرض الكشف الكامل -->
                <a href="{{ route('egypt.secondary.all-results', array_merge(request()->all(), ['top_count' => 'all'])) }}" 
                   class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl text-base">
                    <i class="fa-solid fa-list-ol text-xl"></i>
                    <div class="text-right">
                        <span class="block">📊 عرض كشف الدرجات الكامل</span>
                        <span class="block text-xs opacity-80 mt-1">
                            ({{ isset($systemType) && $systemType ? ($systemType == 'old' ? 'النظام القديم' : 'النظام الحديث') : 'جميع الأنظمة' }} - كل الطلاب)
                        </span>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- تنويه -->
        <div class="mt-6 p-4 bg-amber-50 border-2 border-amber-300 rounded-xl text-center no-print">
            <p class="text-amber-800 font-bold">
                <i class="fa-solid fa-triangle-exclamation ml-2"></i>
                تنبيه: هذا الكشف غير رسمي - قم بمراجعة مدرستك للتأكد من النتيجة
            </p>
        </div>
        
        {{-- Content Section for SEO --}}
        @if(isset($examType) && $examType->show_content_section && ($examType->content_title || $examType->content_body))
        <div class="w-full max-w-6xl mx-auto mt-12">
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 border border-gray-100">
                @if($examType->content_title)
                <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-gray-100">{{ $examType->content_title }}</h2>
                @endif
                @if($examType->content_intro)
                <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed">{{ $examType->content_intro }}</p>
                @endif
                @if($examType->content_body)
                <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                            prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                            prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-emerald-500 prose-h2:pr-4 prose-h2:py-1
                            prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-emerald-700
                            prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                            prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                            prose-a:text-emerald-600 prose-a:hover:text-emerald-700">
                    {!! $examType->getFormattedContentBody() !!}
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- كلمات البحث الشائعة -->
        <div class="max-w-4xl mx-auto">
            @include('partials.popular-keywords')
        </div>
        </div><!-- نهاية screen-content -->
    </div>
</div>

{{-- Print Styles --}}
<style>
/* إخفاء قسم الطباعة في العرض العادي */
.print-only-section {
    display: none !important;
}

/* ===== Print Styles - كشف مفصل للطباعة ===== */
@media print {
    /* إعدادات الصفحة */
    @page {
        size: A4 landscape;
        margin: 8mm;
    }
    
    /* إظهار قسم الطباعة */
    .print-only-section {
        display: block !important;
    }
    
    /* إخفاء المحتوى العادي للشاشة */
    .screen-content {
        display: none !important;
    }
    
    /* إخفاء عناصر التنقل والأزرار */
    .no-print,
    nav,
    button,
    header,
    footer,
    .fixed,
    .sticky {
        display: none !important;
    }
    
    /* إعدادات عامة */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Arial, sans-serif !important;
        font-size: 10pt !important;
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
        direction: rtl !important;
    }
    
    main {
        min-height: auto !important;
    }
    
    .w-full.bg-gradient-to-b,
    .bg-gradient-to-b {
        padding: 0 !important;
        background: white !important;
    }
    
    .w-full.px-3,
    [class*="max-w-"] {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* ===== ترويسة الكشف ===== */
    .print-header {
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 3px double #1e3a5f;
    }
    
    .header-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .header-table td {
        vertical-align: middle;
        padding: 5px;
        border: none !important;
    }
    
    .header-right {
        text-align: right;
        width: 35%;
    }
    
    .header-right .flag-img {
        height: 35px;
        width: auto;
        display: inline-block;
        vertical-align: middle;
        margin-left: 10px;
    }
    
    .header-title {
        font-size: 14pt;
        font-weight: 900;
        color: #1e3a5f;
        display: inline-block;
        vertical-align: middle;
    }
    
    .header-subtitle {
        font-size: 10pt;
        color: #4a5568;
        font-weight: bold;
    }
    
    .header-center {
        text-align: center;
        width: 30%;
    }
    
    .logo-text {
        font-size: 18pt;
        font-weight: 900;
        color: #059669;
    }
    
    .site-url {
        font-size: 9pt;
        color: #6b7280;
        font-weight: bold;
    }
    
    .header-left {
        text-align: left;
        width: 35%;
    }
    
    .year-box {
        display: inline-block;
        background: #f0fdf4;
        border: 2px solid #059669;
        border-radius: 8px;
        padding: 6px 12px;
        text-align: center;
    }
    
    .year-label {
        font-size: 8pt;
        color: #4a5568;
    }
    
    .year-value {
        font-size: 12pt;
        font-weight: 900;
        color: #059669;
    }
    
    /* ===== عنوان الكشف ===== */
    .print-title-section {
        text-align: center;
        margin-bottom: 10px;
    }
    
    .print-main-title {
        font-size: 16pt;
        font-weight: 900;
        color: #1e3a5f;
        margin: 0 0 8px 0;
    }
    
    .print-badges {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .print-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 9pt;
        font-weight: bold;
    }
    
    .badge-system {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }
    
    .badge-branch {
        background: #e0e7ff;
        color: #3730a3;
        border: 1px solid #6366f1;
    }
    
    .badge-count {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }
    
    /* ===== إحصائيات سريعة ===== */
    .print-stats {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 12px;
        padding: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    
    .stat-item {
        text-align: center;
        padding: 0 15px;
        border-left: 1px solid #cbd5e1;
    }
    
    .stat-item:last-child {
        border-left: none;
    }
    
    .stat-label {
        display: block;
        font-size: 8pt;
        color: #64748b;
    }
    
    .stat-value {
        display: block;
        font-size: 12pt;
        font-weight: 900;
        color: #1e3a5f;
    }
    
    /* ===== جدول النتائج ===== */
    .print-results-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        font-size: 9pt;
    }
    
    .print-results-table thead tr {
        background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%) !important;
    }
    
    .print-results-table th {
        color: white !important;
        font-weight: bold;
        padding: 8px 5px;
        text-align: center;
        border: 1px solid #1e3a8a;
        white-space: nowrap;
    }
    
    .print-results-table td {
        padding: 6px 5px;
        text-align: center;
        border: 1px solid #d1d5db;
        vertical-align: middle;
    }
    
    .print-results-table .col-rank {
        width: 35px;
        font-weight: bold;
    }
    
    .print-results-table .col-name {
        text-align: right;
        padding-right: 10px !important;
        font-weight: bold;
        min-width: 180px;
    }
    
    .print-results-table .col-seat {
        font-family: 'Consolas', monospace;
        letter-spacing: 1px;
    }
    
    .print-results-table .col-score {
        font-weight: 900;
        font-size: 11pt;
        color: #1e40af;
    }
    
    .print-results-table .col-percent {
        font-weight: bold;
        color: #059669;
    }
    
    .print-results-table .status-pass {
        background: #d1fae5 !important;
        color: #065f46;
        font-weight: bold;
    }
    
    .print-results-table .status-fail {
        background: #fee2e2 !important;
        color: #991b1b;
        font-weight: bold;
    }
    
    .print-results-table .top-three {
        background: #fef3c7 !important;
    }
    
    .print-results-table .even-row:not(.top-three) {
        background: #f8fafc !important;
    }
    
    .print-results-table .empty-row {
        text-align: center;
        padding: 20px;
        color: #9ca3af;
    }
    
    /* ===== تذييل الكشف ===== */
    .print-footer {
        margin-top: 15px;
        padding-top: 10px;
        border-top: 2px solid #e2e8f0;
        text-align: center;
    }
    
    .footer-note {
        font-size: 9pt;
        color: #b45309;
        background: #fef3c7;
        padding: 6px 15px;
        border-radius: 5px;
        display: inline-block;
        margin-bottom: 8px;
        border: 1px solid #fcd34d;
    }
    
    .footer-info {
        font-size: 8pt;
        color: #6b7280;
    }
    
    .footer-info span {
        margin: 0 5px;
    }
    
    /* تجنب قطع الصفوف */
    .print-results-table tr {
        page-break-inside: avoid;
    }
    
    .print-results-table thead {
        display: table-header-group;
    }
}
</style>
@endsection
