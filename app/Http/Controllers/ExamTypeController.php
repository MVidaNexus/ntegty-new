<?php

namespace App\Http\Controllers;

use App\Models\ExamType;
use App\Services\SeoService;
use App\Services\SchemaService;
use Illuminate\Http\Request;

class ExamTypeController extends Controller
{
    public function __construct(
        private SeoService $seoService
    ) {}

    public function show($id)
    {
        $examType = ExamType::with(['country', 'country.governorates'])->findOrFail($id);
        
        $meta = $this->seoService->generateMetaTags(
            "نتائج {$examType->name_ar} - {$examType->country->name_ar}",
            "استعلم الآن عن نتائج {$examType->name_ar} في جميع محافظات {$examType->country->name_ar} عبر منصة نتيجتي"
        );
        
        // Generate structured data with EducationalOccupationalCredential
        $structuredData = SchemaService::examTypePage($examType, $examType->country->governorates->all());

        return view('exam-types.show', compact('examType', 'meta', 'structuredData'));
    }
}
