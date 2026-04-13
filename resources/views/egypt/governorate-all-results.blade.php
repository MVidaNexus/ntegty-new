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
        <div class="hidden print:block">
            <!-- ترويسة الطباعة -->
            <div class="print-header mb-4 pb-3 border-b-2 border-gray-800">
                <div class="flex items-center justify-between px-4">
                    <!-- شعار نتيجتي + علم مصر - اليمين -->
                    <div class="flex items-center gap-3">
                        <img src="https://flagcdn.com/w160/eg.png" class="h-12 w-auto object-contain" alt="علم مصر">
                        <div class="text-right">
                            <h2 class="text-lg font-black text-gray-900 leading-tight">جمهورية مصر العربية</h2>
                            <p class="text-sm text-gray-700 font-bold">{{ $certName ?? 'الشهادة الإعدادية' }} - محافظة {{ $governorate->name_ar }}</p>
                            <p class="text-xs text-gray-500">{{ $suffix ?? '' }}@if(isset($pageTitle) && $pageTitle) - {{ $pageTitle }}@endif</p>
                        </div>
                    </div>
                    
                    <!-- شعار نتيجتي - الوسط -->
                    <div class="text-center">
                        @if(isset($settings['logo']))
                            <img src="{{ asset('uploads/' . $settings['logo']) }}" class="h-16 w-auto object-contain mx-auto mb-1" alt="نتيجتي">
                        @else
                            <span class="text-2xl font-black text-emerald-700">نتيجتي</span>
                        @endif
                        <p class="text-sm text-gray-700 font-bold">ntegty.com</p>
                    </div>
                    
                    <!-- شعار المحافظة - الشمال -->
                    <div class="text-left">
                        @if($governorate->logo_path)
                            <img src="{{ asset('uploads/' . $governorate->logo_path) }}" class="h-14 w-auto object-contain" alt="{{ $governorate->name_ar }}">
                        @else
                            <div class="h-14 w-14 bg-emerald-100 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-black text-emerald-700">{{ mb_substr($governorate->name_ar, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== الشاشة العادية ==================== -->
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

        <!-- Page Title -->
        <div class="text-center mb-6 no-print">
            @if(isset($pageTitle) && $pageTitle)
                <!-- Dynamic Title for Search (School/Administration) -->
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-5 border-2 border-emerald-200 mb-4">
                    <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-2 leading-tight">
                        <i class="fa-solid fa-search text-emerald-600 ml-2"></i>
                        نتائج {{ $pageTitle }}
                    </h1>
                    <p class="text-base md:text-lg font-bold text-emerald-600 mb-1">
                        {{ $certName ?? 'الشهادة الإعدادية' }} - محافظة {{ $governorate->name_ar }}
                    </p>
                    <p class="text-sm text-gray-500">
                        <i class="fa-solid fa-calendar-alt ml-1"></i>
                        {{ $suffix ?? '' }}
                    </p>
                </div>
            @else
                <!-- Default Title -->
                <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-2 leading-tight">
                    نتائج {{ $certName ?? 'الشهادة الإعدادية' }}
                </h1>
                <p class="text-lg md:text-xl font-bold text-emerald-600 mb-1">
                    محافظة {{ $governorate->name_ar }}
                </p>
                <p class="text-sm text-gray-500">
                    <i class="fa-solid fa-calendar-alt ml-1"></i>
                    {{ $suffix ?? '' }}
                </p>
            @endif
        </div>

        <!-- Action Buttons - Top -->
        @php
            $search = request('search', '');
            $topUrl = route('egypt.governorate.top-results', $governorate);
            $yearParam = request('academic_year_id') ? 'academic_year_id=' . request('academic_year_id') : '';
            if ($search) {
                if (str_contains($search, 'مدرسة') || str_contains($search, 'مدرسه') || str_contains($search, 'اعدادي') || str_contains($search, 'إعدادي') || str_contains($search, 'ابتدائي')) {
                    $firstResult = $results->first();
                    $admin = $firstResult?->subjects_data['الادارة'] ?? $firstResult?->subjects_data['الاداره'] ?? $firstResult?->subjects_data['الإدارة'] ?? '';
                    $topUrl = route('egypt.governorate.top-results', $governorate) . '?' . http_build_query(['type' => 'school', 'name' => $search, 'admin' => $admin]);
                } elseif (str_contains($search, 'إدارة') || str_contains($search, 'ادارة') || str_contains($search, 'التعليمية')) {
                    $topUrl = route('egypt.governorate.top-results', $governorate) . '?' . http_build_query(['type' => 'admin', 'name' => $search]);
                }
            }
        @endphp
        
        <div class="mb-6 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-4 border border-emerald-200 no-print">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <!-- Back Button -->
                @if(isset($pageTitle) && $pageTitle)
                <a href="{{ route('egypt.governorate.all-results', $governorate) }}{{ $yearParam ? '?' . $yearParam : '' }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-arrow-right"></i>
                    <span>العودة لكل النتائج</span>
                </a>
                @else
                <a href="{{ route('egypt.governorate.results', $governorate) }}{{ $yearParam ? '?' . $yearParam : '' }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-search"></i>
                    <span>البحث عن طالب</span>
                </a>
                @endif
                
                <!-- Quick Actions -->
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-sm text-emerald-700 font-medium hidden sm:inline">
                        <i class="fa-solid fa-bolt ml-1"></i>
                        إجراءات سريعة:
                    </span>
                    
                    <!-- Top Results Button -->
                    <a href="{{ $topUrl }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg hover:scale-105">
                        <i class="fa-solid fa-trophy"></i>
                        <span>عرض الأوائل</span>
                    </a>
                    
                    <!-- Print Button -->
                    <button type="button" onclick="window.print()" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg hover:scale-105 cursor-pointer">
                        <i class="fa-solid fa-print"></i>
                        <span>طباعة الصفحة</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 no-print">
            <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-emerald-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-users text-emerald-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">إجمالي النتائج</p>
                        <p class="text-2xl font-black text-gray-800">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-green-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">ناجح</p>
                        <p class="text-2xl font-black text-green-600">{{ number_format($stats['passed']) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-red-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">راسب</p>
                        <p class="text-2xl font-black text-red-600">{{ number_format($stats['failed']) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-blue-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-trophy text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">أعلى مجموع</p>
                        <p class="text-2xl font-black text-blue-600">{{ $stats['highest'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter (hidden from UI but works via URL) -->
        <div class="hidden">
            <form id="searchForm" method="GET" action="{{ url()->current() }}">
                <input type="hidden" name="search" value="{{ request('search', '') }}">
                @if(request('academic_year_id'))
                <input type="hidden" name="academic_year_id" value="{{ request('academic_year_id') }}">
                @endif
            </form>
        </div>

        <!-- ==================== جدول النتائج (كشف بيان الدرجات) ==================== -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden print:shadow-none print:border-2 print:border-gray-800 print:rounded-none">
            <!-- عنوان الجدول للطباعة -->
            <div class="hidden print:block bg-gray-100 px-3 py-2 border-b-2 border-gray-800">
                <div class="flex justify-between items-center text-xs">
                    <span>إجمالي: {{ number_format($stats['total']) }} طالب</span>
                    <span class="font-bold">كشف بيان الدرجات</span>
                    <span>ناجح: {{ number_format($stats['passed']) }} | راسب: {{ number_format($stats['failed']) }}</span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full print:text-xs">
                    <thead class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white print:bg-gray-800 print:text-white">
                        <tr>
                            <th class="px-2 py-2 text-right text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">#</th>
                            <th class="px-2 py-2 text-right text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">رقم الجلوس</th>
                            <th class="px-2 py-2 text-right text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">الاسم</th>
                            @if($hasAdministration ?? false)
                                <th class="px-2 py-2 text-center text-xs font-bold whitespace-nowrap bg-emerald-700/50 print:px-1 print:py-1 print:text-[7pt] print:bg-gray-700">الإدارة</th>
                            @endif
                            @if($hasSchool ?? false)
                                <th class="px-2 py-2 text-center text-xs font-bold whitespace-nowrap bg-emerald-700/50 print:px-1 print:py-1 print:text-[7pt] print:bg-gray-700">المدرسة</th>
                            @endif
                            @foreach($subjects as $subject)
                                <th class="px-2 py-2 text-center text-xs font-bold whitespace-nowrap print:px-1 print:py-1 print:text-[7pt]">{{ $subject }}</th>
                            @endforeach
                            <th class="px-2 py-2 text-center text-xs font-bold bg-emerald-700 print:px-1 print:py-1 print:text-[8pt] print:bg-gray-900">المجموع</th>
                            <th class="px-2 py-2 text-center text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">الحالة</th>
                            <th class="px-2 py-2 text-center text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">الترتيب</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 print:divide-gray-400">
                        @php
                            $absentMarkers = $examType?->getAbsentMarkers() ?? \App\Models\ExamType::DEFAULT_ABSENT_MARKERS;
                        @endphp
                        @forelse($results as $index => $result)
                        @php
                            $administration = $result->subjects_data['الادارة'] ?? $result->subjects_data['الاداره'] ?? $result->subjects_data['الإدارة'] ?? $result->subjects_data['الإداره'] ?? '-';
                            $school = $result->subjects_data['المدرسة'] ?? $result->subjects_data['المدرسه'] ?? '-';
                            $rankNum = $result->rank ?? '-';
                            $isTopTen = is_numeric($rankNum) && $rankNum <= 10;
                        @endphp
                        <tr class="hover:bg-emerald-50/50 transition-colors {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }} {{ $isTopTen ? 'print:bg-yellow-50' : '' }} print:hover:bg-transparent">
                            <td class="px-2 py-2 text-gray-500 text-xs print:px-1 print:py-1 print:text-[8pt]">{{ $results->firstItem() + $index }}</td>
                            <td class="px-2 py-2 font-bold text-gray-800 text-sm print:px-1 print:py-1 print:text-[8pt]">
                                <a href="/egypt/preparatory/{{ $governorate->slug }}/{{ $academicYear->year ?? '2024-2025' }}/{{ isset($semester) && $semester == 2 ? 'term2' : 'term1' }}/{{ $result->seat_number }}" 
                                   class="hover:text-emerald-600 hover:underline no-print">
                                    {{ $result->seat_number }}
                                </a>
                                <span class="hidden print:inline">{{ $result->seat_number }}</span>
                            </td>
                            <td class="px-2 py-2 font-semibold text-gray-800 text-sm print:px-1 print:py-1 print:text-[8pt] print:max-w-[80px] print:truncate">
                                <a href="/egypt/preparatory/{{ $governorate->slug }}/{{ $academicYear->year ?? '2024-2025' }}/{{ isset($semester) && $semester == 2 ? 'term2' : 'term1' }}/{{ $result->seat_number }}" 
                                   class="hover:text-emerald-600 hover:underline no-print">
                                    {{ $result->student_name }}
                                </a>
                                <span class="hidden print:inline">{{ $result->student_name }}</span>
                                @if($isTopTen)
                                    <span class="inline-flex items-center mr-1 no-print">
                                        @if($rankNum == 1)
                                            <i class="fa-solid fa-medal text-yellow-500"></i>
                                        @elseif($rankNum == 2)
                                            <i class="fa-solid fa-medal text-gray-400"></i>
                                        @elseif($rankNum == 3)
                                            <i class="fa-solid fa-medal text-amber-600"></i>
                                        @else
                                            <i class="fa-solid fa-star text-xs text-amber-500"></i>
                                        @endif
                                    </span>
                                @endif
                            </td>
                            @if($hasAdministration ?? false)
                                <td class="px-2 py-2 text-center text-xs text-gray-600 bg-gray-50 print:px-1 print:py-1 print:text-[7pt] print:max-w-[60px] print:truncate">{{ $administration }}</td>
                            @endif
                            @if($hasSchool ?? false)
                                <td class="px-2 py-2 text-center text-xs text-gray-600 bg-gray-50 max-w-[150px] truncate print:px-1 print:py-1 print:text-[7pt] print:max-w-[70px]" title="{{ $school }}">{{ $school }}</td>
                            @endif
                            @foreach($subjects as $subject)
                                @php
                                    $subjectScore = $result->subjects_data[$subject] ?? '-';
                                    $scoreStr = trim((string)$subjectScore);
                                    $scoreLower = mb_strtolower($scoreStr);
                                    $isAbsent = false;
                                    foreach ($absentMarkers as $marker) {
                                        if ($scoreLower === mb_strtolower(trim($marker))) {
                                            $isAbsent = true;
                                            break;
                                        }
                                    }
                                    if (!$isAbsent && mb_strlen($scoreStr) <= 5 && str_starts_with($scoreStr, 'غ')) {
                                        $isAbsent = true;
                                    }
                                @endphp
                                <td class="px-2 py-2 text-center text-xs font-medium print:px-1 print:py-1 print:text-[8pt] {{ $isAbsent ? 'text-red-500 bg-red-50 print:text-red-600' : 'text-gray-700' }}">
                                    {{ $isAbsent ? 'غ' : $subjectScore }}
                                </td>
                            @endforeach
                            <td class="px-2 py-2 text-center font-black text-base text-emerald-600 bg-emerald-50 print:px-1 print:py-1 print:text-[9pt] print:text-black print:bg-gray-100">
                                {{ $result->total_score }}
                            </td>
                            <td class="px-2 py-2 text-center print:px-1 print:py-1">
                                @php
                                    $status = $result->status;
                                    $semester = $result->semester ?? 0;
                                    if ($examType && $examType->auto_calculate_status) {
                                        $status = $examType->calculateStatus($result->total_score, $semester);
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold print:px-1 print:py-0 print:text-[7pt] print:rounded-none
                                    {{ $status === 'ناجح' ? 'bg-green-100 text-green-700 print:bg-transparent print:text-green-700' : 'bg-red-100 text-red-700 print:bg-transparent print:text-red-700' }}">
                                    {{ $status ?: '-' }}
                                </span>
                            </td>
                            <td class="px-2 py-2 text-center print:px-1 print:py-1">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold print:w-auto print:h-auto print:rounded-none print:text-[8pt]
                                    {{ $isTopTen ? 'bg-amber-100 text-amber-700 print:text-amber-700 print:font-black' : 'bg-blue-100 text-blue-700 print:bg-transparent' }}">
                                    {{ $rankNum }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($subjects) + 6 + (($hasAdministration ?? false) ? 1 : 0) + (($hasSchool ?? false) ? 1 : 0) }}" class="px-4 py-12 text-center text-gray-500">
                                <i class="fa-solid fa-inbox text-4xl mb-3 block"></i>
                                لا توجد نتائج
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($results->hasPages())
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 no-print">
                {{ $results->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        <!-- تذييل الطباعة -->
        <div class="hidden print:block mt-4 pt-3 border-t-2 border-gray-800">
            <div class="flex items-center justify-between px-4">
                <!-- التحذير -->
                <p class="text-sm font-bold text-red-600"><i class="fa-solid fa-triangle-exclamation"></i> هذا الكشف غير رسمي - راجع مدرستك <i class="fa-solid fa-triangle-exclamation"></i></p>
                
                <!-- لوجو نتيجتي والدومين -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-black text-emerald-700">نتيجتي</span>
                    <span class="text-xs text-gray-500">|</span>
                    <span class="text-xs font-bold text-gray-700">ntegty.com</span>
                </div>
                
                <!-- رقم الصفحة -->
                <p class="text-xs text-gray-500">صفحة {{ $results->currentPage() }} من {{ $results->lastPage() }}</p>
            </div>
        </div>

        <!-- تنويه للشاشة -->
        <div class="mt-6 p-4 bg-amber-50 border-2 border-amber-300 rounded-xl text-center no-print">
            <p class="text-amber-800 font-bold">
                <i class="fa-solid fa-triangle-exclamation ml-2"></i>
                تنبيه: هذا الكشف غير رسمي - قم بمراجعة مدرستك للتأكد من النتيجة
            </p>
        </div>

        <!-- Action Buttons - Bottom -->
        <div class="mt-6 bg-gradient-to-r from-slate-50 to-gray-50 rounded-xl p-5 border border-gray-200 no-print">
            <p class="text-center text-gray-600 text-sm mb-4">
                <i class="fa-solid fa-hand-point-down ml-2 text-emerald-600"></i>
                للاطلاع على تفاصيل أكثر أو طباعة الكشف، اختر من الأزرار التالية:
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <!-- Top Results Button -->
                <a href="{{ $topUrl }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105">
                    <i class="fa-solid fa-trophy text-lg"></i>
                    <span>العشرة الأوائل</span>
                </a>
                
                <!-- Print Button -->
                <button type="button" onclick="window.print()" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105 cursor-pointer">
                    <i class="fa-solid fa-print text-lg"></i>
                    <span>طباعة الصفحة</span>
                </button>
                
                <!-- Search Button -->
                <a href="{{ route('egypt.governorate.results', $governorate) }}{{ $yearParam ? '?' . $yearParam : '' }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl transition-all shadow-lg hover:shadow-xl border-2 border-gray-200 hover:border-emerald-300">
                    <i class="fa-solid fa-search text-lg text-emerald-600"></i>
                    <span>البحث عن طالب</span>
                </a>
            </div>
        </div>
        
        {{-- Governorate Content Section for SEO (First) --}}
        @if(isset($governorate) && $governorate->show_content_section && ($governorate->content_title || $governorate->content_body))
        <div class="w-full max-w-6xl mx-auto mt-8 no-print">
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 border border-gray-100 overflow-hidden">
                @if($governorate->content_title)
                <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-gray-100">{{ $governorate->content_title }}</h2>
                @endif
                @if($governorate->content_intro)
                <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed">{{ $governorate->content_intro }}</p>
                @endif
                @if($governorate->content_body)
                {{-- Isolated content wrapper with scoped styles --}}
                <article class="gov-content-body-all">
                    {!! $governorate->getFormattedContentBody() !!}
                </article>
                <style>
                    .gov-content-body-all { color: #374151; line-height: 1.7; font-size: 1rem; }
                    .gov-content-body-all * { max-width: 100%; box-sizing: border-box; }
                    .gov-content-body-all h1, .gov-content-body-all h2, .gov-content-body-all h3, .gov-content-body-all h4, .gov-content-body-all h5, .gov-content-body-all h6 { font-weight: 700; color: #1f2937; margin-top: 1.25rem; margin-bottom: 0.5rem; line-height: 1.4; }
                    .gov-content-body-all h2 { font-size: 1.375rem; border-right: 4px solid #10b981; padding-right: 0.75rem; }
                    .gov-content-body-all h3 { font-size: 1.125rem; color: #047857; }
                    .gov-content-body-all p { margin-bottom: 0.75rem; line-height: 1.7; }
                    .gov-content-body-all ul, .gov-content-body-all ol { margin: 0.5rem 0; padding-right: 1.25rem; }
                    .gov-content-body-all ul { list-style-type: disc; }
                    .gov-content-body-all ol { list-style-type: decimal; }
                    .gov-content-body-all li { margin-bottom: 0.25rem; line-height: 1.6; padding: 0.125rem 0; }
                    .gov-content-body-all li p { margin-bottom: 0.25rem; }
                    .gov-content-body-all li br { display: none; }
                    .gov-content-body-all strong { font-weight: 700; color: #111827; }
                    .gov-content-body-all a { color: #059669; text-decoration: underline; }
                    .gov-content-body-all table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.95rem; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                    .gov-content-body-all table th, .gov-content-body-all table td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: right; }
                    .gov-content-body-all table th { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 700; }
                    .gov-content-body-all table tbody tr:nth-child(even) { background-color: #f9fafb; }
                    .gov-content-body-all table tbody tr:hover { background-color: #ecfdf5; }
                    .gov-content-body-all br + br { display: none; }
                    .gov-content-body-all > br:first-child { display: none; }
                </style>
                @endif
            </div>
        </div>
        @endif
        
        {{-- Internal Linking Section for All Egyptian Governorates (SEO Optimized) --}}
        @if(isset($governorate))
            @include('partials.governorates-internal-links', ['currentGovernorateSlug' => $governorate->slug ?? null])
        @endif
        
        {{-- Content Section for SEO (Second) - Hidden for Preparatory certificate --}}
        @if(isset($examType) && $examType->show_content_section && ($examType->content_title || $examType->content_body) && !str_contains($examType->code ?? '', 'preparatory'))
        <div class="w-full max-w-6xl mx-auto mt-8 no-print">
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
        <div class="max-w-4xl mx-auto no-print">
            @include('partials.popular-keywords')
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    /* إعدادات الصفحة - عرضي */
    @page { 
        size: A4 landscape;
        margin: 5mm;
    }
    
    /* إعدادات عامة */
    * { 
        -webkit-print-color-adjust: exact !important; 
        print-color-adjust: exact !important;
        box-sizing: border-box !important;
    }
    
    body, html { 
        background: white !important; 
        margin: 0 !important;
        padding: 0 !important;
        font-size: 10pt !important;
    }
    
    /* إخفاء العناصر غير المطلوبة */
    .no-print,
    nav,
    header,
    footer,
    .breadcrumbs,
    .pagination {
        display: none !important;
    }
    
    /* إظهار عناصر الطباعة */
    .print\\:block {
        display: block !important;
    }
    .print\\:inline {
        display: inline !important;
    }
    
    /* ترويسة الطباعة */
    .print-header {
        page-break-after: avoid;
    }
    .print-header h1 {
        font-size: 14pt !important;
    }
    .print-header h2 {
        font-size: 12pt !important;
    }
    .print-header h3 {
        font-size: 11pt !important;
    }
    
    /* الجدول */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 7pt !important;
    }
    
    thead {
        display: table-header-group !important;
    }
    
    th, td {
        border: 1px solid #333 !important;
        padding: 2px 3px !important;
    }
    
    th {
        background: #333 !important;
        color: white !important;
        font-size: 7pt !important;
        white-space: nowrap !important;
    }
    
    td {
        font-size: 7pt !important;
    }
    
    /* صفوف الأوائل */
    tr.print\\:bg-yellow-50 {
        background: #fef9c3 !important;
    }
    
    /* الحاوية */
    .max-w-\\[1400px\\] {
        max-width: none !important;
    }
    
    /* منع انقسام الصفوف */
    tr {
        page-break-inside: avoid !important;
    }
}
</style>
@endsection
