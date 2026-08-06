<?php

namespace App\Http\Controllers;

use App\Models\PreRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PreRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'exam_type_slug' => 'required|string|max:100',
            'seat_number' => 'nullable|string|max:50',
            'governorate_slug' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Avoid exact duplicates (same phone and exam)
        if ($request->filled('phone')) {
            $exists = PreRegistration::where('phone', $request->phone)
                ->where('exam_type_slug', $request->exam_type_slug)
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'success' => true,
                    'message' => 'أنت مسجل بالفعل لدينا، سيتم إبلاغك فور ظهور النتيجة.'
                ]);
            }
        }

        PreRegistration::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل بياناتك بنجاح! سنقوم بإبلاغك فور اعتماد النتيجة.'
        ]);
    }
}
