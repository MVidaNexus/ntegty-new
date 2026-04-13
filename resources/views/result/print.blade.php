<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتيجة {{ $result->student_name }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            padding: 20px;
            background: white;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .info-item {
            padding: 10px;
        }
        
        .info-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 20px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 12px;
            text-align: right;
        }
        
        th {
            background: #333;
            color: white;
            font-weight: bold;
        }
        
        td.score {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }
        
        tfoot td {
            background: #f0f0f0;
            font-weight: bold;
            font-size: 20px;
        }
        
        .status {
            text-align: center;
            margin: 30px 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 15px 40px;
            font-size: 24px;
            font-weight: bold;
            border: 3px solid;
            border-radius: 10px;
        }
        
        .status-pass {
            color: #059669;
            border-color: #059669;
            background: #d1fae5;
        }
        
        .status-fail {
            color: #dc2626;
            border-color: #dc2626;
            background: #fee2e2;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #333;
            color: #666;
            font-size: 14px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .container {
                border: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>نتيجة {{ $result->examType->name_ar }}</h1>
            <p>{{ $result->governorate->name_ar }} - {{ $result->academicYear->year }}</p>
        </div>

        <!-- Student Info -->
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">اسم الطالب</div>
                <div class="info-value">{{ $result->student_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">رقم الجلوس</div>
                <div class="info-value">{{ $result->seat_number }}</div>
            </div>
        </div>
        
        @php
            $subjects = $result->subjects_data;
            $school = $subjects['المدرسة'] ?? $subjects['المدرسه'] ?? $subjects['SCHOOL'] ?? $subjects['School'] ?? $subjects['school'] ?? null;
            $administration = $subjects['الإدارة'] ?? $subjects['الاداره'] ?? $subjects['الادارة'] ?? $subjects['EDARA'] ?? $subjects['Edara'] ?? $subjects['edara'] ?? null;
        @endphp
        
        @if($school || $administration)
        <div class="info-grid" style="margin-top: 10px;">
            @if($administration)
            <div class="info-item">
                <div class="info-label">الإدارة التعليمية</div>
                <div class="info-value" style="font-size: 14px;">{{ $administration }}</div>
            </div>
            @endif
            @if($school)
            <div class="info-item">
                <div class="info-label">المدرسة</div>
                <div class="info-value" style="font-size: 14px;">{{ $school }}</div>
            </div>
            @endif
        </div>
        @endif

        <!-- Subjects Table -->
        <table>
            <thead>
                <tr>
                    <th>المادة</th>
                    <th style="text-align: center; width: 150px;">الدرجة</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $absentMarkers = $result->examType?->getAbsentMarkers() ?? \App\Models\ExamType::DEFAULT_ABSENT_MARKERS;
                    // Fields to skip (not actual subjects)
                    $skipFields = ['المدرسة', 'المدرسه', 'الإدارة', 'الاداره', 'الادارة', 'المركز', 'الاسم', 'رقم الجلوس', 'الملاحظات', 'الحالة', 'EDARA', 'SCHOOL', 'school', 'edara', 'School', 'Edara'];
                @endphp
                @foreach($result->subjects_data as $subject => $score)
                @php
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
                    <td>{{ $subject }}</td>
                    <td class="score" @if($isAbsent) style="color: red;" @endif>{{ $isAbsent ? 'غائب' : $score }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>المجموع الكلي</td>
                    <td class="score" style="color: #2563eb;">{{ $result->total_score }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Status -->
        <div class="status">
            <div class="status-badge {{ $result->status === 'ناجح' ? 'status-pass' : 'status-fail' }}">
                {{ $result->status }}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>نتيجتي - منصة نتائج الطلاب في الوطن العربي</p>
            <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
        </div>
    </div>
</body>
</html>
