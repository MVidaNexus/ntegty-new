@extends('layouts.layout')

@php
    // استخدام الإعدادات من قاعدة البيانات أو القيم الافتراضية
    $s = $settings ?? null;
    $fontFamily = $s?->font_family ?? 'Cairo';
    
    // Meta & Schema
    $pageTitle = $s?->page_title ?? 'تصميم شهادة تقدير للمتفوقين';
    $pageDesc = $s?->page_description ?? 'اصنع ذكرى جميلة لنجاحك واحتفظ بشهادة تقدير بتصميم احترافي';
    $meta = [
        'title' => $pageTitle . ' | نتيجتي',
        'description' => $pageDesc,
        'og_title' => $pageTitle,
        'og_description' => $pageDesc,
    ];
    $structuredData = \App\Services\SchemaService::certificatePage();
@endphp

@section('structured_data')
{!! $structuredData !!}
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $fontFamily) }}:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-purple-50/30 py-8" x-data="certificateGenerator()">
    <div class="container mx-auto px-4 mb-8">
        <div class="text-center max-w-5xl mx-auto">
            <!-- Icon -->
            <div class="mb-6 animate-bounce-slow">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-amber-400 via-orange-500 to-rose-500 rounded-3xl shadow-2xl shadow-orange-500/30 transform hover:scale-110 transition-all duration-300">
                    <i class="fa-solid fa-trophy text-4xl text-white"></i>
                </div>
            </div>
            
            <!-- Title -->
            <h1 class="text-2xl md:text-4xl lg:text-5xl font-black mb-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent leading-loose py-4 px-2">
                {{ $s?->page_title ?? 'تصميم شهادة تقدير للمتفوقين' }}
            </h1>
            
            <!-- Description -->
            <p class="text-base md:text-xl text-slate-600 font-medium max-w-3xl mx-auto leading-relaxed">
                {{ $s?->page_description ?? 'اصنع ذكرى جميلة لنجاحك واحتفظ بشهادة تقدير بتصميم احترافي في ثوانٍ ✨' }}
            </p>
            
            <!-- Decorative Line -->
            <div class="flex items-center justify-center gap-3 mt-6">
                <div class="h-1 w-16 bg-gradient-to-r from-transparent via-purple-500 to-transparent rounded-full"></div>
                <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                <div class="h-1 w-16 bg-gradient-to-r from-transparent via-purple-500 to-transparent rounded-full"></div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-6 max-w-7xl mx-auto">
            <!-- Sidebar -->
            <div class="w-full lg:w-1/3 space-y-4">
                <div class="bg-white rounded-2xl shadow-xl p-5 border-2 border-blue-100">
                    <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2 border-b-2 border-blue-100 pb-3">
                        <span><i class="fa-solid fa-pen-to-square"></i></span><span>بيانات الشهادة</span>
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold mb-2"><i class="fa-solid fa-user"></i> اسم الطالب</label>
                            <input type="text" x-model="studentName" @input="drawCertificate" class="w-full px-4 py-3 border-2 rounded-xl text-center font-bold" placeholder="علي محمود حسين">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2"><i class="fa-solid fa-school"></i> المدرسة / المحافظة</label>
                            <input type="text" x-model="schoolName" @input="drawCertificate" class="w-full px-4 py-3 border-2 rounded-xl text-center" placeholder="مدرسة النصر - القاهرة">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2"><i class="fa-solid fa-book"></i> نوع النتيجة</label>
                            <input type="text" x-model="examType" @input="drawCertificate" class="w-full px-4 py-3 border-2 rounded-xl text-center" placeholder="الصف الثالث الإعدادي">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2"><i class="fa-solid fa-star"></i> المجموع</label>
                            <input type="text" x-model="totalScore" @input="drawCertificate" class="w-full px-4 py-3 border-2 rounded-xl text-center font-bold" placeholder="280">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2"><i class="fa-solid fa-chart-pie"></i> من</label>
                            <input type="text" x-model="maxScore" @input="drawCertificate" class="w-full px-4 py-3 border-2 rounded-xl text-center font-bold" placeholder="280">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2"><i class="fa-solid fa-percent"></i> النسبة المئوية</label>
                            <input type="text" x-model="percentage" @input="drawCertificate" class="w-full px-4 py-3 border-2 rounded-xl text-center font-bold" placeholder="95%">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2"><i class="fa-solid fa-hashtag"></i> رقم الجلوس</label>
                            <input type="text" x-model="seatNumber" @input="drawCertificate" class="w-full px-4 py-3 border-2 rounded-xl text-center font-bold" placeholder="12345">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2"><i class="fa-solid fa-users"></i> الجنس</label>
                            <div class="flex gap-3">
                                <button @click="gender = 'male'; drawCertificate()" class="flex-1 py-3 rounded-xl font-bold transition-all" :class="gender === 'male' ? 'bg-blue-500 text-white scale-105' : 'bg-gray-100'">ذكر</button>
                                <button @click="gender = 'female'; drawCertificate()" class="flex-1 py-3 rounded-xl font-bold transition-all" :class="gender === 'female' ? 'bg-pink-500 text-white scale-105' : 'bg-gray-100'">أنثى</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- زر التحميل -->
                <button @click="downloadCertificate" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-black py-4 rounded-2xl shadow-xl transition-all hover:scale-[1.02] hover:shadow-green-500/30">
                    <span class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-download text-xl"></i>
                        <span>تحميل الشهادة</span>
                    </span>
                </button>
                
                <!-- زر الطباعة -->
                <button @click="printCertificate" class="w-full mt-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-black py-4 rounded-2xl shadow-xl transition-all hover:scale-[1.02] hover:shadow-blue-500/30">
                    <span class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print text-xl"></i>
                        <span>طباعة الشهادة</span>
                    </span>
                </button>
            </div>

            <!-- Preview -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-3xl p-4 shadow-2xl">
                    <canvas id="certificateCanvas" class="w-full rounded-xl shadow-lg"></canvas>
                    <p class="text-center text-sm text-gray-500 mt-3">
                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></span>
                        المعاينة المباشرة - يتم التحديث تلقائياً
                    </p>
                </div>
                
                <!-- Share Buttons -->
                <div class="mt-6 bg-white rounded-2xl p-6 shadow-xl border-2 border-purple-100/50">
                    <h3 class="text-lg font-black text-slate-800 mb-4 text-center flex items-center justify-center gap-2">
                        <i class="fa-solid fa-share-nodes text-purple-600"></i>
                        شارك فرحة النجاح مع أصدقائك
                    </h3>
                    <div class="flex flex-wrap justify-center gap-3 md:gap-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="flex items-center gap-2 bg-[#1877F2] text-white px-5 py-3 rounded-xl hover:opacity-90 hover:-translate-y-1 transition-all font-bold shadow-md hover:shadow-[#1877F2]/30">
                            <i class="fa-brands fa-facebook-f"></i> <span class="hidden md:inline">فيسبوك</span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text=حصلت على شهادة تقدير احترافية من موقع نتيجتي! 🎓%0Aجربها الآن مجاناً 👇" target="_blank" class="flex items-center gap-2 bg-black text-white px-5 py-3 rounded-xl hover:bg-gray-800 hover:-translate-y-1 transition-all font-bold shadow-md hover:shadow-black/30">
                            <i class="fa-brands fa-x-twitter"></i> <span class="hidden md:inline">تويتر</span>
                        </a>
                        <a href="https://wa.me/?text=موقع نتيجتي بيعمل شهادات تقدير احترافية مجاناً! 🎓%0Aجربه من هنا: {{ url()->current() }}" target="_blank" class="flex items-center gap-2 bg-[#25D366] text-white px-5 py-3 rounded-xl hover:opacity-90 hover:-translate-y-1 transition-all font-bold shadow-md hover:shadow-[#25D366]/30">
                            <i class="fa-brands fa-whatsapp text-lg"></i> <span class="hidden md:inline">واتساب</span>
                        </a>
                        <button @click="navigator.clipboard.writeText(window.location.href); $el.innerHTML = '<i class=\'fa-solid fa-check\'></i> تم النسخ';" class="flex items-center gap-2 bg-slate-100 text-slate-600 px-5 py-3 rounded-xl hover:bg-slate-200 hover:-translate-y-1 transition-all font-bold shadow-sm border border-slate-200">
                            <i class="fa-solid fa-link"></i> <span class="hidden md:inline">نسخ الرابط</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('certificateGenerator', () => ({
        // بيانات المستخدم
        studentName: @json(request('name', '')),
        schoolName: @json(request('school', '')),
        examType: @json(request('type', '')),
        totalScore: @json(request('score', '')),
        maxScore: @json(request('max', request('total', ''))),
        percentage: @json(request('percentage', '')),
        seatNumber: @json(request('seat', '')),
        status: @json(request('status', '')),
        gender: 'male',
        
        // إعدادات Canvas
        canvas: null,
        ctx: null,
        width: {{ $s?->canvas_width ?? 2480 }},
        height: {{ $s?->canvas_height ?? 1754 }},
        bgImage: null,
        
        // إعدادات من قاعدة البيانات - البنية الجديدة مع 5 أسطر
        settings: {
            // أسطر الذكور
            line1TextMale: @json($s?->line1_text_male ?? 'تتقدم إدارة المدرسة والهيئة التعليمية بخالص التهاني والتبريكات'),
            line2TextMale: @json($s?->line2_text_male ?? 'للطالب المتفوق {student_name}'),
            line3TextMale: @json($s?->line3_text_male ?? 'وذلك لحصوله على مجموع {total_score} من {max_score}'),
            line4TextMale: @json($s?->line4_text_male ?? 'في {exam_type}'),
            line5TextMale: @json($s?->line5_text_male ?? 'متمنين له دوام التوفيق والنجاح'),
            line6TextMale: @json($s?->line6_text_male ?? ''),
            // أسطر الإناث
            line1TextFemale: @json($s?->line1_text_female ?? 'تتقدم إدارة المدرسة والهيئة التعليمية بخالص التهاني والتبريكات'),
            line2TextFemale: @json($s?->line2_text_female ?? 'للطالبة المتفوقة {student_name}'),
            line3TextFemale: @json($s?->line3_text_female ?? 'وذلك لحصولها على مجموع {total_score} من {max_score}'),
            line4TextFemale: @json($s?->line4_text_female ?? 'في {exam_type}'),
            line5TextFemale: @json($s?->line5_text_female ?? 'متمنين لها دوام التوفيق والنجاح'),
            line6TextFemale: @json($s?->line6_text_female ?? ''),
            
            // تنسيق كل سطر - ذكور
            line1FontFamily: @json($s?->line1_font_family ?? 'Cairo'),
            line1FontSize: {{ $s?->line1_font_size ?? 50 }},
            line1Color: @json($s?->line1_color ?? '#374151'),
            line1PositionX: {{ $s?->line1_position_x ?? 1240 }},
            line1PositionY: {{ $s?->line1_position_y ?? 900 }},
            line2FontFamily: @json($s?->line2_font_family ?? 'Cairo'),
            line2FontSize: {{ $s?->line2_font_size ?? 50 }},
            line2Color: @json($s?->line2_color ?? '#374151'),
            line2PositionX: {{ $s?->line2_position_x ?? 1240 }},
            line2PositionY: {{ $s?->line2_position_y ?? 1000 }},
            line3FontFamily: @json($s?->line3_font_family ?? 'Cairo'),
            line3FontSize: {{ $s?->line3_font_size ?? 50 }},
            line3Color: @json($s?->line3_color ?? '#374151'),
            line3PositionX: {{ $s?->line3_position_x ?? 1240 }},
            line3PositionY: {{ $s?->line3_position_y ?? 1100 }},
            line4FontFamily: @json($s?->line4_font_family ?? 'Cairo'),
            line4FontSize: {{ $s?->line4_font_size ?? 50 }},
            line4Color: @json($s?->line4_color ?? '#374151'),
            line4PositionX: {{ $s?->line4_position_x ?? 1240 }},
            line4PositionY: {{ $s?->line4_position_y ?? 1200 }},
            line5FontFamily: @json($s?->line5_font_family ?? 'Cairo'),
            line5FontSize: {{ $s?->line5_font_size ?? 50 }},
            line5Color: @json($s?->line5_color ?? '#374151'),
            line5PositionX: {{ $s?->line5_position_x ?? 1240 }},
            line5PositionY: {{ $s?->line5_position_y ?? 1300 }},
            line6FontFamily: @json($s?->line6_font_family ?? 'Cairo'),
            line6FontSize: {{ $s?->line6_font_size ?? 50 }},
            line6Color: @json($s?->line6_color ?? '#374151'),
            line6PositionX: {{ $s?->line6_position_x ?? 1240 }},
            line6PositionY: {{ $s?->line6_position_y ?? 1400 }},
            
            // تنسيق كل سطر - إناث
            line1FontFamilyFemale: @json($s?->line1_font_family_female ?? $s?->line1_font_family ?? 'Cairo'),
            line1FontSizeFemale: {{ $s?->line1_font_size_female ?? $s?->line1_font_size ?? 50 }},
            line1ColorFemale: @json($s?->line1_color_female ?? $s?->line1_color ?? '#374151'),
            line1PositionXFemale: {{ $s?->line1_position_x_female ?? $s?->line1_position_x ?? 1240 }},
            line1PositionYFemale: {{ $s?->line1_position_y_female ?? $s?->line1_position_y ?? 900 }},
            line2FontFamilyFemale: @json($s?->line2_font_family_female ?? $s?->line2_font_family ?? 'Cairo'),
            line2FontSizeFemale: {{ $s?->line2_font_size_female ?? $s?->line2_font_size ?? 50 }},
            line2ColorFemale: @json($s?->line2_color_female ?? $s?->line2_color ?? '#374151'),
            line2PositionXFemale: {{ $s?->line2_position_x_female ?? $s?->line2_position_x ?? 1240 }},
            line2PositionYFemale: {{ $s?->line2_position_y_female ?? $s?->line2_position_y ?? 1000 }},
            line3FontFamilyFemale: @json($s?->line3_font_family_female ?? $s?->line3_font_family ?? 'Cairo'),
            line3FontSizeFemale: {{ $s?->line3_font_size_female ?? $s?->line3_font_size ?? 50 }},
            line3ColorFemale: @json($s?->line3_color_female ?? $s?->line3_color ?? '#374151'),
            line3PositionXFemale: {{ $s?->line3_position_x_female ?? $s?->line3_position_x ?? 1240 }},
            line3PositionYFemale: {{ $s?->line3_position_y_female ?? $s?->line3_position_y ?? 1100 }},
            line4FontFamilyFemale: @json($s?->line4_font_family_female ?? $s?->line4_font_family ?? 'Cairo'),
            line4FontSizeFemale: {{ $s?->line4_font_size_female ?? $s?->line4_font_size ?? 50 }},
            line4ColorFemale: @json($s?->line4_color_female ?? $s?->line4_color ?? '#374151'),
            line4PositionXFemale: {{ $s?->line4_position_x_female ?? $s?->line4_position_x ?? 1240 }},
            line4PositionYFemale: {{ $s?->line4_position_y_female ?? $s?->line4_position_y ?? 1200 }},
            line5FontFamilyFemale: @json($s?->line5_font_family_female ?? $s?->line5_font_family ?? 'Cairo'),
            line5FontSizeFemale: {{ $s?->line5_font_size_female ?? $s?->line5_font_size ?? 50 }},
            line5ColorFemale: @json($s?->line5_color_female ?? $s?->line5_color ?? '#374151'),
            line5PositionXFemale: {{ $s?->line5_position_x_female ?? $s?->line5_position_x ?? 1240 }},
            line5PositionYFemale: {{ $s?->line5_position_y_female ?? $s?->line5_position_y ?? 1300 }},
            line6FontFamilyFemale: @json($s?->line6_font_family_female ?? $s?->line6_font_family ?? 'Cairo'),
            line6FontSizeFemale: {{ $s?->line6_font_size_female ?? $s?->line6_font_size ?? 50 }},
            line6ColorFemale: @json($s?->line6_color_female ?? $s?->line6_color ?? '#374151'),
            line6PositionXFemale: {{ $s?->line6_position_x_female ?? $s?->line6_position_x ?? 1240 }},
            line6PositionYFemale: {{ $s?->line6_position_y_female ?? $s?->line6_position_y ?? 1400 }},
            
            // التوقيعات
            signatureLeftText: @json($s?->signature_left_text ?? 'مدير المدرسة'),
            signatureLeftFontFamily: @json($s?->signature_left_font_family ?? 'Cairo'),
            signatureLeftFontSize: {{ $s?->signature_left_font_size ?? 45 }},
            signatureLeftColor: @json($s?->signature_left_color ?? '#1e3a8a'),
            signatureLeftPositionX: {{ $s?->signature_left_position_x ?? 620 }},
            signatureLeftPositionY: {{ $s?->signature_left_position_y ?? 1500 }},
            
            signatureRightText: @json($s?->signature_right_text ?? 'الكادر الإداري'),
            signatureRightFontFamily: @json($s?->signature_right_font_family ?? 'Cairo'),
            signatureRightFontSize: {{ $s?->signature_right_font_size ?? 45 }},
            signatureRightColor: @json($s?->signature_right_color ?? '#1e3a8a'),
            signatureRightPositionX: {{ $s?->signature_right_position_x ?? 1860 }},
            signatureRightPositionY: {{ $s?->signature_right_position_y ?? 1500 }},
            
            // إعدادات الاسم - ذكور
            namePositionX: {{ $s?->name_position_x ?? 1240 }},
            namePositionY: {{ $s?->name_position_y ?? 700 }},
            // إعدادات الاسم - إناث
            namePositionXFemale: {{ $s?->name_position_x_female ?? $s?->name_position_x ?? 1240 }},
            namePositionYFemale: {{ $s?->name_position_y_female ?? $s?->name_position_y ?? 700 }},
            nameFontFamily: @json($s?->name_font_family ?? 'Cairo'),
            nameFontSize: {{ $s?->name_font_size ?? 80 }},
            primaryColor: @json($s?->primary_color ?? '#1e3a8a'),
            fontFamily: @json($s?->font_family ?? $s?->name_font_family ?? 'Cairo'),
            
            showDate: {{ $s?->show_date ?? true ? 'true' : 'false' }},
        },
        
        init() {
            this.canvas = document.getElementById('certificateCanvas');
            this.ctx = this.canvas.getContext('2d');
            this.canvas.width = this.width;
            this.canvas.height = this.height;
            this.loadTemplate();
            
            // Watch for changes to redraw
            this.$watch('studentName', () => this.drawCertificate());
            this.$watch('schoolName', () => this.drawCertificate());
            this.$watch('examType', () => this.drawCertificate());
            this.$watch('totalScore', () => this.drawCertificate());
            this.$watch('maxScore', () => this.drawCertificate());
            this.$watch('percentage', () => this.drawCertificate());
            this.$watch('seatNumber', () => this.drawCertificate());
            this.$watch('gender', () => this.drawCertificate());
        },
        
        loadTemplate() {
            this.bgImage = new Image();
            this.bgImage.crossOrigin = 'anonymous';
            
            // استخدام الصورة من الإعدادات أو الصورة الافتراضية
            @if($s?->background_image)
            this.bgImage.src = '{{ url("public/storage/" . $s->background_image) }}';
            @else
            this.bgImage.src = '{{ url("public/images/certificate_template_v2.png") }}';
            @endif
            
            this.bgImage.onload = () => {
                this.drawCertificate();
            };
            
            this.bgImage.onerror = () => {
                // Placeholder if image missing
                const ctx = this.ctx;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, this.width, this.height);
                ctx.fillStyle = '#e2e8f0'; 
                ctx.fillRect(50, 50, this.width-100, this.height-100);
                
                ctx.fillStyle = '#ef4444';
                ctx.font = 'bold 60px "Cairo", sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('يرجى رفع صورة خلفية الشهادة', this.width/2, this.height/2);
                ctx.font = '40px "Cairo", sans-serif';
                ctx.fillStyle = '#64748b';
                ctx.fillText('من لوحة التحكم: إعدادات الموقع > شهادة التقدير', this.width/2, this.height/2 + 80);
            };
        },
        
        drawCertificate() {
            if (!this.bgImage || !this.bgImage.complete) return;
            
            const ctx = this.ctx;
            const w = this.width;
            const h = this.height;
            const s = this.settings;
            
            // 1. Draw Background
            ctx.clearRect(0, 0, w, h);
            ctx.drawImage(this.bgImage, 0, 0, w, h);
            
            // 2. Settings
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            // Helper function to replace variables in text
            const replaceVariables = (text) => {
                return text
                    .replace(/\{student_name\}/g, this.studentName || 'الاسم هنا')
                    .replace(/\{school_name\}/g, this.schoolName || '')
                    .replace(/\{exam_type\}/g, this.examType || '')
                    .replace(/\{total_score\}/g, this.totalScore || '')
                    .replace(/\{max_score\}/g, this.maxScore || '')
                    .replace(/\{percentage\}/g, this.percentage || '')
                    .replace(/\{seat_number\}/g, this.seatNumber || '')
                    .replace(/\{date\}/g, new Date().toLocaleDateString('ar-EG'));
            };
            
            // 3. Draw Student Name - بناءً على الجنس
            const nameX = this.gender === 'male' ? s.namePositionX : s.namePositionXFemale;
            const nameY = this.gender === 'male' ? s.namePositionY : s.namePositionYFemale;
            ctx.font = `bold ${s.nameFontSize}px "${s.nameFontFamily}", "Tajawal", sans-serif`;
            ctx.fillStyle = s.primaryColor;
            ctx.shadowColor = "rgba(0,0,0,0.1)";
            ctx.shadowBlur = 10;
            ctx.fillText(this.studentName || 'الاسم هنا', nameX, nameY);
            ctx.shadowBlur = 0;
            
            // 4. Draw 6 Text Lines - بناءً على الجنس
            const isMale = this.gender === 'male';
            const lines = [
                { 
                    text: isMale ? s.line1TextMale : s.line1TextFemale, 
                    font: isMale ? s.line1FontFamily : s.line1FontFamilyFemale, 
                    size: isMale ? s.line1FontSize : s.line1FontSizeFemale, 
                    color: isMale ? s.line1Color : s.line1ColorFemale, 
                    x: isMale ? s.line1PositionX : s.line1PositionXFemale, 
                    y: isMale ? s.line1PositionY : s.line1PositionYFemale 
                },
                { 
                    text: isMale ? s.line2TextMale : s.line2TextFemale, 
                    font: isMale ? s.line2FontFamily : s.line2FontFamilyFemale, 
                    size: isMale ? s.line2FontSize : s.line2FontSizeFemale, 
                    color: isMale ? s.line2Color : s.line2ColorFemale, 
                    x: isMale ? s.line2PositionX : s.line2PositionXFemale, 
                    y: isMale ? s.line2PositionY : s.line2PositionYFemale 
                },
                { 
                    text: isMale ? s.line3TextMale : s.line3TextFemale, 
                    font: isMale ? s.line3FontFamily : s.line3FontFamilyFemale, 
                    size: isMale ? s.line3FontSize : s.line3FontSizeFemale, 
                    color: isMale ? s.line3Color : s.line3ColorFemale, 
                    x: isMale ? s.line3PositionX : s.line3PositionXFemale, 
                    y: isMale ? s.line3PositionY : s.line3PositionYFemale 
                },
                { 
                    text: isMale ? s.line4TextMale : s.line4TextFemale, 
                    font: isMale ? s.line4FontFamily : s.line4FontFamilyFemale, 
                    size: isMale ? s.line4FontSize : s.line4FontSizeFemale, 
                    color: isMale ? s.line4Color : s.line4ColorFemale, 
                    x: isMale ? s.line4PositionX : s.line4PositionXFemale, 
                    y: isMale ? s.line4PositionY : s.line4PositionYFemale 
                },
                { 
                    text: isMale ? s.line5TextMale : s.line5TextFemale, 
                    font: isMale ? s.line5FontFamily : s.line5FontFamilyFemale, 
                    size: isMale ? s.line5FontSize : s.line5FontSizeFemale, 
                    color: isMale ? s.line5Color : s.line5ColorFemale, 
                    x: isMale ? s.line5PositionX : s.line5PositionXFemale, 
                    y: isMale ? s.line5PositionY : s.line5PositionYFemale 
                },
                { 
                    text: isMale ? s.line6TextMale : s.line6TextFemale, 
                    font: isMale ? s.line6FontFamily : s.line6FontFamilyFemale, 
                    size: isMale ? s.line6FontSize : s.line6FontSizeFemale, 
                    color: isMale ? s.line6Color : s.line6ColorFemale, 
                    x: isMale ? s.line6PositionX : s.line6PositionXFemale, 
                    y: isMale ? s.line6PositionY : s.line6PositionYFemale 
                },
            ];
            lines.forEach(line => {
                if (line.text && line.text.trim()) {
                    ctx.font = `bold ${line.size}px "${line.font}", "Tajawal", sans-serif`;
                    ctx.fillStyle = line.color;
                    ctx.fillText(replaceVariables(line.text), line.x, line.y);
                }
            });
            
            // 5. Signature Area
            // Left Signature
            ctx.font = `bold ${s.signatureLeftFontSize}px "${s.signatureLeftFontFamily}", "Tajawal", sans-serif`;
            ctx.fillStyle = s.signatureLeftColor;
            ctx.fillText(s.signatureLeftText, s.signatureLeftPositionX, s.signatureLeftPositionY);
            
            // Draw a line for signature
            ctx.beginPath();
            ctx.moveTo(s.signatureLeftPositionX - 150, s.signatureLeftPositionY + 50);
            ctx.lineTo(s.signatureLeftPositionX + 150, s.signatureLeftPositionY + 50);
            ctx.strokeStyle = s.signatureLeftColor;
            ctx.lineWidth = 3;
            ctx.stroke();
            
            // Right Signature
            ctx.font = `bold ${s.signatureRightFontSize}px "${s.signatureRightFontFamily}", "Tajawal", sans-serif`;
            ctx.fillStyle = s.signatureRightColor;
            ctx.fillText(s.signatureRightText, s.signatureRightPositionX, s.signatureRightPositionY);
            
            // Draw School Name above right signature
            if (this.schoolName) {
                ctx.font = `bold 45px "${s.fontFamily}", "Tajawal", sans-serif`;
                ctx.fillStyle = s.signatureRightColor;
                ctx.fillText(this.schoolName, s.signatureRightPositionX, s.signatureRightPositionY - 60);
            }
            
            // 6. Date
            if (s.showDate) {
                const date = new Date().toLocaleDateString('ar-EG');
                ctx.font = `bold 30px "${s.fontFamily}", sans-serif`;
                ctx.fillStyle = '#9ca3af';
                ctx.fillText(`تحريراً في: ${date}`, w / 2, h - 100);
            }
        },
        
        downloadCertificate() {
            const link = document.createElement('a');
            link.download = `شهادة_${this.studentName || 'تقدير'}.png`;
            link.href = this.canvas.toDataURL('image/png', 1.0);
            link.click();
        },
        
        printCertificate() {
            const printWindow = window.open('', '_blank');
            const imageData = this.canvas.toDataURL('image/png', 1.0);
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html dir="rtl" lang="ar">
                <head>
                    <meta charset="UTF-8">
                    <title>طباعة شهادة - ${this.studentName || 'تقدير'}</title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        @page { size: A4 landscape; margin: 0; }
                        body {
                            width: 100%; height: 100vh;
                            display: flex; justify-content: center; align-items: center;
                            background: white;
                        }
                        .certificate-container {
                            width: 100%; height: 100%;
                            display: flex; justify-content: center; align-items: center;
                        }
                        .certificate-image {
                            max-width: 100%; max-height: 100vh;
                            width: auto; height: auto; object-fit: contain;
                        }
                        @media print {
                            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                            .certificate-image { width: 100%; height: 100vh; object-fit: contain; }
                        }
                    </style>
                </head>
                <body>
                    <div class="certificate-container">
                        <img src="${imageData}" alt="شهادة تقدير" class="certificate-image" onload="window.print(); setTimeout(() => window.close(), 500);">
                    </div>
                </body>
                </html>
            `);
            
            printWindow.document.close();
        }
    }));
});
</script>
@endpush
@endsection
