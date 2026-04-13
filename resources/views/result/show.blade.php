@extends('layouts.layout')

@php
    $meta = [
        'title' => "نتيجة {$result->student_name} - {$result->examType->name_ar} | نتيجتي",
        'description' => "نتيجة الطالب {$result->student_name} في {$result->examType->name_ar} - رقم الجلوس: {$result->seat_number}",
        'og_title' => "نتيجة {$result->student_name} - {$result->examType->name_ar}",
        'og_description' => "نتيجة الطالب {$result->student_name} في {$result->examType->name_ar}",
    ];
@endphp

@section('structured_data')
@if(isset($structuredData))
{!! $structuredData !!}
@endif
@endsection

@section('meta')
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Result Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 print:shadow-none">
            <!-- Header -->
            <div class="text-center mb-8 print:mb-4">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    نتيجة {{ $result->examType->name_ar }}
                </h1>
                <p class="text-gray-600">
                    {{ $result->governorate->name_ar }} - {{ $result->academicYear->year }}
                </p>
            </div>

            <!-- Student Info -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 mb-6">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">اسم الطالب</p>
                        <p class="text-xl font-bold text-gray-800">{{ $result->student_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">رقم الجلوس</p>
                        <p class="text-xl font-bold text-gray-800">{{ $result->seat_number }}</p>
                    </div>
                </div>
                
                @php
                    $subjects = $result->subjects_data;
                    $school = $subjects['المدرسة'] ?? $subjects['المدرسه'] ?? $subjects['SCHOOL'] ?? $subjects['School'] ?? $subjects['school'] ?? null;
                    $administration = $subjects['الإدارة'] ?? $subjects['الاداره'] ?? $subjects['الادارة'] ?? $subjects['EDARA'] ?? $subjects['Edara'] ?? $subjects['edara'] ?? null;
                @endphp
                
                @if($school || $administration)
                <div class="grid md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-blue-200">
                    @if($administration)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">الإدارة التعليمية</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $administration }}</p>
                    </div>
                    @endif
                    @if($school)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">المدرسة</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $school }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Subjects Table -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">درجات المواد</h2>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-4 py-3 text-right">المادة</th>
                                <th class="border border-gray-300 px-4 py-3 text-center">الدرجة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $absentMarkers = $result->examType?->getAbsentMarkers() ?? \App\Models\ExamType::DEFAULT_ABSENT_MARKERS;
                                // Fields to skip (not actual subjects)
                                $skipFields = ['المدرسة', 'المدرسه', 'الإدارة', 'الاداره', 'الادارة', 'المركز', 'الاسم', 'رقم الجلوس', 'الملاحظات', 'الحالة', 'EDARA', 'SCHOOL', 'school', 'edara', 'School', 'Edara'];
                                // Excluded subjects from exam type
                                $excludedSubjects = $result->examType?->excluded_subjects ?? [];
                                if (is_string($excludedSubjects)) {
                                    $excludedSubjects = json_decode($excludedSubjects, true) ?: [];
                                }
                                
                                // Separate subjects into added and excluded
                                // IMPORTANT: Negative scores are now HIDDEN completely, not shown anywhere
                                $addedSubjects = [];
                                $excludedSubjectsList = [];
                                
                                foreach($result->subjects_data as $subject => $score) {
                                    // Skip non-subject fields
                                    $shouldSkip = false;
                                    $subjectLower = mb_strtolower(trim($subject));
                                    foreach ($skipFields as $skip) {
                                        if ($subjectLower === mb_strtolower($skip) || str_contains($subjectLower, mb_strtolower($skip))) {
                                            $shouldSkip = true;
                                            break;
                                        }
                                    }
                                    if ($shouldSkip) continue;
                                    
                                    // HIDE negative scores completely - don't show them anywhere
                                    if (is_numeric($score) && floatval($score) < 0) {
                                        continue; // Skip negative scores entirely
                                    }
                                    
                                    // Check if subject is excluded by name
                                    $isExcluded = false;
                                    $normalizedSubject = str_replace(['ى', 'أ', 'إ', 'آ', 'ة'], ['ي', 'ا', 'ا', 'ا', 'ه'], mb_strtolower(trim($subject)));
                                    
                                    // Check by name
                                    foreach ($excludedSubjects as $excluded) {
                                        $normalizedExcluded = str_replace(['ى', 'أ', 'إ', 'آ', 'ة'], ['ي', 'ا', 'ا', 'ا', 'ه'], mb_strtolower(trim($excluded)));
                                        if ($normalizedSubject === $normalizedExcluded || str_starts_with($normalizedSubject, $normalizedExcluded . ' ')) {
                                            $isExcluded = true;
                                            break;
                                        }
                                    }
                                    
                                    if ($isExcluded) {
                                        $excludedSubjectsList[$subject] = $score;
                                    } else {
                                        $addedSubjects[$subject] = $score;
                                    }
                                }
                            @endphp
                            @foreach($addedSubjects as $subject => $score)
                            @php
                                $scoreStr = trim((string)$score);
                                $scoreLower = mb_strtolower($scoreStr);
                                $isAbsent = false;
                                foreach ($absentMarkers as $marker) {
                                    if ($scoreLower === mb_strtolower(trim($marker))) {
                                        $isAbsent = true;
                                        break;
                                    }
                                }
                                if (!$isAbsent && str_starts_with($scoreStr, 'غ')) {
                                    $isAbsent = true;
                                }
                            @endphp
                            <tr>
                                <td class="border border-gray-300 px-4 py-3">{{ $subject }}</td>
                                <td class="border border-gray-300 px-4 py-3 text-center font-bold {{ $isAbsent ? 'text-red-500 bg-red-50' : '' }}">
                                    {{ $isAbsent ? 'غائب' : $score }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-blue-50">
                                <td class="border border-gray-300 px-4 py-3 font-bold">المجموع الكلي</td>
                                <td class="border border-gray-300 px-4 py-3 text-center font-bold text-blue-600 text-xl">
                                    {{ $result->total_score }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                {{-- Excluded Subjects Section --}}
                @if(count($excludedSubjectsList) > 0)
                <div class="mt-6">
                    <h3 class="text-lg font-bold text-amber-700 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        مواد لا تُضاف للمجموع
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-amber-50">
                                    <th class="border border-amber-300 px-4 py-3 text-right">المادة</th>
                                    <th class="border border-amber-300 px-4 py-3 text-center">الدرجة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($excludedSubjectsList as $subject => $score)
                                @php
                                    $scoreStr = trim((string)$score);
                                    $scoreLower = mb_strtolower($scoreStr);
                                    $isAbsent = false;
                                    foreach ($absentMarkers as $marker) {
                                        if ($scoreLower === mb_strtolower(trim($marker))) {
                                            $isAbsent = true;
                                            break;
                                        }
                                    }
                                    if (!$isAbsent && str_starts_with($scoreStr, 'غ')) {
                                        $isAbsent = true;
                                    }
                                    // For negative scores, show absolute value
                                    $displayScore = $score;
                                    if (is_numeric($score) && floatval($score) < 0) {
                                        $displayScore = abs(floatval($score));
                                    }
                                @endphp
                                <tr class="bg-amber-50/50">
                                    <td class="border border-amber-300 px-4 py-3 text-amber-800">{{ $subject }}</td>
                                    <td class="border border-amber-300 px-4 py-3 text-center font-bold {{ $isAbsent ? 'text-red-500' : 'text-amber-600' }}">
                                        {{ $isAbsent ? 'غائب' : $displayScore }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Status -->
            <div class="text-center mb-6">
                <span class="inline-block px-8 py-3 rounded-full text-xl font-bold {{ $result->status === 'ناجح' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $result->status }}
                </span>
            </div>

            <!-- Actions -->
            <div class="flex justify-center gap-4 no-print">
                <a href="{{ route('result.print', $result->id) }}" 
                   target="_blank"
                   class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> طباعة النتيجة
                </a>
                <button onclick="window.history.back()" 
                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-right"></i> رجوع
                </button>
            </div>
        </div>
    </div>

    <!-- كلمات البحث الشائعة -->
    <div class="max-w-4xl mx-auto no-print">
        @include('partials.popular-keywords')
    </div>
</div>
@endsection
