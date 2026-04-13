@php
    $record = $getRecord();
    $bgImage = $record?->background_image;
    $bgUrl = $bgImage ? url('public/storage/' . $bgImage) : '';
    
    // تحديد الجنس
    $gender = $gender ?? 'male';
    $isFemale = $gender === 'female';
    $suffix = $isFemale ? '_female' : '';
    
    // القيم التجريبية للمتغيرات في المعاينة (تُستبدل في العرض فقط)
    $previewVariables = [
        '{student_name}' => $isFemale ? 'فاطمة أحمد محمد' : 'محمد أحمد علي',
        '{school_name}' => 'مدرسة النصر',
        '{exam_type}' => 'الشهادة الإعدادية',
        '{total_score}' => '280',
        '{max_score}' => '280',
        '{percentage}' => '100%',
        '{seat_number}' => '12345',
        '{status}' => 'ناجح',
        '{governorate}' => 'القاهرة',
        '{year}' => date('Y'),
    ];
    
    // الحقول التي سنتحكم بها
    $fieldPrefix = $isFemale ? '_female' : '';
    
    // جمع البيانات للـ JavaScript
    $settings = [
        'canvasWidth' => $record?->canvas_width ?? 2480,
        'canvasHeight' => $record?->canvas_height ?? 1754,
        'bgUrl' => $bgUrl,
        'gender' => $gender,
        'isFemale' => $isFemale,
        'fieldPrefix' => $fieldPrefix,
        
        // الاسم
        'namePositionX' => $isFemale ? ($record?->name_position_x_female ?? $record?->name_position_x ?? 1240) : ($record?->name_position_x ?? 1240),
        'namePositionY' => $isFemale ? ($record?->name_position_y_female ?? $record?->name_position_y ?? 700) : ($record?->name_position_y ?? 700),
        'nameFontSize' => $record?->name_font_size ?? 80,
        'nameFontFamily' => $record?->name_font_family ?? 'Cairo',
        'primaryColor' => $record?->primary_color ?? '#1e3a8a',
        'studentName' => $isFemale ? 'فاطمة أحمد محمد' : 'محمد أحمد علي',
        
        // الأسطر
        'lines' => [],
    ];
    
    for ($i = 1; $i <= 6; $i++) {
        // الحصول على النص من قاعدة البيانات (بدون افتراضي - يعرض ما أدخله المستخدم فقط)
        $rawText = $isFemale 
            ? ($record?->{"line{$i}_text_female"} ?? '')
            : ($record?->{"line{$i}_text_male"} ?? '');
        
        // استبدال المتغيرات بالقيم التجريبية للعرض فقط
        $displayText = $rawText ? str_replace(
            array_keys($previewVariables),
            array_values($previewVariables),
            $rawText
        ) : '';
        
        $settings['lines'][$i] = [
            'text' => $displayText,
            'rawText' => $rawText,
            'positionX' => $isFemale 
                ? ($record?->{"line{$i}_position_x_female"} ?? $record?->{"line{$i}_position_x"} ?? 1240)
                : ($record?->{"line{$i}_position_x"} ?? 1240),
            'positionY' => $isFemale 
                ? ($record?->{"line{$i}_position_y_female"} ?? $record?->{"line{$i}_position_y"} ?? (800 + $i * 100))
                : ($record?->{"line{$i}_position_y"} ?? (800 + $i * 100)),
            'fontSize' => $isFemale 
                ? ($record?->{"line{$i}_font_size_female"} ?? $record?->{"line{$i}_font_size"} ?? 50)
                : ($record?->{"line{$i}_font_size"} ?? 50),
            'fontFamily' => $isFemale 
                ? ($record?->{"line{$i}_font_family_female"} ?? $record?->{"line{$i}_font_family"} ?? 'Cairo')
                : ($record?->{"line{$i}_font_family"} ?? 'Cairo'),
            'color' => $isFemale 
                ? ($record?->{"line{$i}_color_female"} ?? $record?->{"line{$i}_color"} ?? '#374151')
                : ($record?->{"line{$i}_color"} ?? '#374151'),
        ];
    }
    
    // التوقيعات
    $settings['signatureLeft'] = [
        'text' => $record?->signature_left_text ?? '',
        'positionX' => $record?->signature_left_position_x ?? 620,
        'positionY' => $record?->signature_left_position_y ?? 1500,
        'fontSize' => $record?->signature_left_font_size ?? 45,
        'fontFamily' => $record?->signature_left_font_family ?? 'Cairo',
        'color' => $record?->signature_left_color ?? '#1e3a8a',
    ];
    
    $settings['signatureRight'] = [
        'text' => $record?->signature_right_text ?? '',
        'positionX' => $record?->signature_right_position_x ?? 1860,
        'positionY' => $record?->signature_right_position_y ?? 1500,
        'fontSize' => $record?->signature_right_font_size ?? 45,
        'fontFamily' => $record?->signature_right_font_family ?? 'Cairo',
        'color' => $record?->signature_right_color ?? '#1e3a8a',
    ];
    
    // اسم الـ state path في Filament
    $statePath = $getStatePath();
@endphp

<div 
    x-data="certificateEditor(@js($settings), $wire)"
    x-init="init()"
    class="w-full certificate-editor"
    wire:ignore.self
>
    <!-- تعليمات الاستخدام -->
    <div class="mb-4 p-3 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-300 dark:border-green-800 rounded-xl">
        <p class="text-sm text-green-800 dark:text-green-200 flex items-center gap-2">
            <span class="text-lg">✅</span>
            <span><strong>جديد:</strong> الآن عند تغيير المواضع، يتم الحفظ تلقائياً! فقط اضغط زر "حفظ" أعلى الصفحة بعد الانتهاء.</span>
        </p>
    </div>
    
    <!-- المتغيرات المتاحة -->
    <div class="mb-4 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
        <p class="text-sm font-bold text-blue-800 dark:text-blue-200 mb-2">📝 المتغيرات المتاحة للنصوص:</p>
        <div class="flex flex-wrap gap-2 text-xs">
            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded border">{student_name}</code>
            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded border">{school_name}</code>
            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded border">{exam_type}</code>
            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded border">{total_score}</code>
            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded border">{max_score}</code>
            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded border">{percentage}</code>
            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded border">{seat_number}</code>
            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded border">{status}</code>
            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded border">{governorate}</code>
            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded border">{year}</code>
        </div>
    </div>
    
    <!-- شريط التحكم -->
    <div class="flex flex-wrap items-center justify-between mb-4 p-3 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-xl border border-blue-200 dark:border-gray-600 gap-3">
        <div class="flex flex-wrap items-center gap-4">
            <!-- تكبير/تصغير -->
            <div class="flex items-center gap-2 bg-white dark:bg-gray-800 rounded-lg px-3 py-2 shadow-sm">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">🔍</span>
                <input type="range" x-model="scale" min="0.2" max="0.6" step="0.05" class="w-24 accent-blue-500">
                <span class="text-sm font-bold min-w-[45px] text-center" x-text="Math.round(scale * 100) + '%'"></span>
            </div>
            
            <!-- نوع الجنس -->
            <div class="flex items-center gap-2 bg-white dark:bg-gray-800 rounded-lg px-3 py-2 shadow-sm">
                <span class="px-3 py-1 rounded-lg font-bold text-sm {{ $isFemale ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ $isFemale ? '👩 أنثى' : '👨 ذكر' }}
                </span>
            </div>
            
            <!-- العنصر النشط -->
            <div class="flex items-center gap-2 bg-white dark:bg-gray-800 rounded-lg px-3 py-2 shadow-sm" x-show="selectedElement">
                <span class="text-sm text-gray-500">العنصر:</span>
                <span class="font-bold text-blue-600" x-text="selectedElementLabel"></span>
            </div>
        </div>
        
        <div class="text-sm text-gray-500 bg-white dark:bg-gray-800 rounded-lg px-3 py-2 shadow-sm">
            📐 <span class="font-mono font-bold" x-text="canvasWidth + ' × ' + canvasHeight"></span> px
        </div>
    </div>

    <!-- حاوية المعاينة مع السحب -->
    <div class="border-2 border-gray-300 dark:border-gray-600 rounded-2xl bg-gray-100 dark:bg-gray-900 p-4 overflow-auto" style="max-height: 70vh;">
        <div 
            class="relative mx-auto shadow-2xl rounded-xl overflow-hidden cursor-crosshair"
            :style="{
                width: (canvasWidth * scale) + 'px',
                height: (canvasHeight * scale) + 'px',
                backgroundColor: '#fff'
            }"
            @mousedown="startDrag($event)"
            @mousemove="onDrag($event)"
            @mouseup="stopDrag()"
            @mouseleave="stopDrag()"
        >
            <!-- صورة الخلفية -->
            <template x-if="bgUrl">
                <img :src="bgUrl" class="absolute inset-0 w-full h-full object-cover pointer-events-none" alt="خلفية">
            </template>
            <template x-if="!bgUrl">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-100 via-yellow-50 to-orange-100 flex items-center justify-center pointer-events-none">
                    <div class="text-center text-gray-400">
                        <div class="text-4xl mb-2">🖼️</div>
                        <p class="text-sm">ارفع صورة الخلفية من تبويب "التصميم"</p>
                    </div>
                </div>
            </template>

            <!-- عنصر الاسم (قابل للسحب) -->
            <div 
                class="absolute cursor-move select-none transition-shadow"
                :class="{ 'ring-4 ring-blue-500 ring-opacity-50 rounded-lg': selectedElement === 'name' }"
                :style="{
                    left: (namePositionX * scale) + 'px',
                    top: (namePositionY * scale) + 'px',
                    transform: 'translateX(-50%)',
                    fontSize: (nameFontSize * scale) + 'px',
                    color: primaryColor,
                    fontFamily: nameFontFamily + ', sans-serif',
                    fontWeight: 'bold',
                    textShadow: '2px 2px 4px rgba(0,0,0,0.1)',
                    zIndex: selectedElement === 'name' ? 100 : 10
                }"
                @mousedown.stop="selectElement('name', $event)"
                x-text="studentName"
            >
            </div>

            <!-- الأسطر الستة (قابلة للسحب) -->
            @for($i = 1; $i <= 6; $i++)
            <template x-if="lines[{{ $i }}] && lines[{{ $i }}].text">
                <div 
                    class="absolute cursor-move select-none transition-shadow whitespace-nowrap"
                    :class="{ 'ring-4 ring-green-500 ring-opacity-50 rounded-lg': selectedElement === 'line{{ $i }}' }"
                    :style="{
                        left: (lines[{{ $i }}].positionX * scale) + 'px',
                        top: (lines[{{ $i }}].positionY * scale) + 'px',
                        transform: 'translateX(-50%)',
                        fontSize: (lines[{{ $i }}].fontSize * scale) + 'px',
                        color: lines[{{ $i }}].color,
                        fontFamily: lines[{{ $i }}].fontFamily + ', sans-serif',
                        fontWeight: 'bold',
                        zIndex: selectedElement === 'line{{ $i }}' ? 100 : {{ 10 - $i }}
                    }"
                    @mousedown.stop="selectElement('line{{ $i }}', $event)"
                    x-text="lines[{{ $i }}].text"
                >
                </div>
            </template>
            @endfor
            
            <!-- التوقيع الأيسر (قابل للسحب) -->
            <template x-if="signatureLeft && signatureLeft.text">
                <div 
                    class="absolute cursor-move select-none transition-shadow whitespace-nowrap"
                    :class="{ 'ring-4 ring-purple-500 ring-opacity-50 rounded-lg': selectedElement === 'signatureLeft' }"
                    :style="{
                        left: (signatureLeft.positionX * scale) + 'px',
                        top: (signatureLeft.positionY * scale) + 'px',
                        transform: 'translateX(-50%)',
                        fontSize: (signatureLeft.fontSize * scale) + 'px',
                        color: signatureLeft.color,
                        fontFamily: signatureLeft.fontFamily + ', sans-serif',
                        fontWeight: 'bold',
                        zIndex: selectedElement === 'signatureLeft' ? 100 : 5
                    }"
                    @mousedown.stop="selectElement('signatureLeft', $event)"
                    x-text="signatureLeft.text"
                >
                </div>
            </template>
            
            <!-- التوقيع الأيمن (قابل للسحب) -->
            <template x-if="signatureRight && signatureRight.text">
                <div 
                    class="absolute cursor-move select-none transition-shadow whitespace-nowrap"
                    :class="{ 'ring-4 ring-orange-500 ring-opacity-50 rounded-lg': selectedElement === 'signatureRight' }"
                    :style="{
                        left: (signatureRight.positionX * scale) + 'px',
                        top: (signatureRight.positionY * scale) + 'px',
                        transform: 'translateX(-50%)',
                        fontSize: (signatureRight.fontSize * scale) + 'px',
                        color: signatureRight.color,
                        fontFamily: signatureRight.fontFamily + ', sans-serif',
                        fontWeight: 'bold',
                        zIndex: selectedElement === 'signatureRight' ? 100 : 5
                    }"
                    @mousedown.stop="selectElement('signatureRight', $event)"
                    x-text="signatureRight.text"
                >
                </div>
            </template>
        </div>
    </div>

    <!-- لوحة التحكم السفلية -->
    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- تحكم العنصر المحدد -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg border border-gray-200 dark:border-gray-700">
            <h4 class="font-bold text-lg mb-3 flex items-center gap-2">
                <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">🎯</span>
                <span x-text="selectedElement ? 'تحكم: ' + selectedElementLabel : 'اختر عنصراً للتحكم'"></span>
            </h4>
            
            <template x-if="selectedElement">
                <div class="space-y-4">
                    <!-- الموضع -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-gray-600 dark:text-gray-400 block mb-1">الموضع الأفقي (X)</label>
                            <div class="flex items-center gap-2">
                                <input type="range" x-model="currentX" min="0" :max="canvasWidth" class="flex-1 accent-blue-500" @input="updatePosition()">
                                <input type="number" x-model="currentX" @input="updatePosition()" class="w-20 text-center text-sm font-mono bg-gray-100 dark:bg-gray-700 rounded px-2 py-1 border-0">
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 dark:text-gray-400 block mb-1">الموضع الرأسي (Y)</label>
                            <div class="flex items-center gap-2">
                                <input type="range" x-model="currentY" min="0" :max="canvasHeight" class="flex-1 accent-blue-500" @input="updatePosition()">
                                <input type="number" x-model="currentY" @input="updatePosition()" class="w-20 text-center text-sm font-mono bg-gray-100 dark:bg-gray-700 rounded px-2 py-1 border-0">
                            </div>
                        </div>
                    </div>
                    
                    <!-- حجم الخط -->
                    <div>
                        <label class="text-sm text-gray-600 dark:text-gray-400 block mb-1">حجم الخط</label>
                        <div class="flex items-center gap-2">
                            <input type="range" x-model="currentFontSize" min="20" max="150" class="flex-1 accent-purple-500" @input="updateFontSize()">
                            <input type="number" x-model="currentFontSize" @input="updateFontSize()" class="w-20 text-center text-sm font-mono bg-gray-100 dark:bg-gray-700 rounded px-2 py-1 border-0">
                        </div>
                    </div>
                    
                    <!-- أزرار التحكم -->
                    <div class="grid grid-cols-2 gap-2">
                        <button 
                            type="button"
                            @click="applyChanges()"
                            class="bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold py-2 px-4 rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg text-sm"
                        >
                            ✅ تطبيق التغييرات
                        </button>
                        <button 
                            type="button"
                            @click="copyValues()"
                            class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg text-sm"
                        >
                            📋 نسخ القيم
                        </button>
                    </div>
                    
                    <!-- عرض القيم الحالية للنسخ -->
                    <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-xs font-mono" x-show="selectedElement">
                        <p class="text-gray-600 dark:text-gray-400">القيم الحالية (انسخها للحقول أعلاه):</p>
                        <p class="text-blue-600 dark:text-blue-400" x-text="'X: ' + currentX + ' | Y: ' + currentY + ' | حجم الخط: ' + currentFontSize"></p>
                    </div>
                </div>
            </template>
            
            <template x-if="!selectedElement">
                <div class="text-center text-gray-400 py-8">
                    <div class="text-4xl mb-2">👆</div>
                    <p>انقر على أي عنصر في المعاينة للتحكم به</p>
                    <p class="text-sm mt-2">يمكنك أيضاً سحب العناصر مباشرة</p>
                </div>
            </template>
        </div>

        <!-- معلومات المواضع -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg border border-gray-200 dark:border-gray-700">
            <h4 class="font-bold text-lg mb-3 flex items-center gap-2">
                <span class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">📍</span>
                مواضع العناصر الحالية
            </h4>
            
            <div class="space-y-2 text-sm max-h-64 overflow-y-auto">
                <div class="flex justify-between items-center p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <span class="font-medium">الاسم</span>
                    <span class="font-mono text-blue-600" x-text="'X:' + namePositionX + ' Y:' + namePositionY"></span>
                </div>
                @for($i = 1; $i <= 6; $i++)
                <template x-if="lines[{{ $i }}] && lines[{{ $i }}].text">
                    <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="font-medium">السطر {{ $i }}</span>
                        <span class="font-mono text-gray-600" x-text="'X:' + lines[{{ $i }}].positionX + ' Y:' + lines[{{ $i }}].positionY"></span>
                    </div>
                </template>
                @endfor
                
                <!-- التوقيعات -->
                <template x-if="signatureLeft && signatureLeft.text">
                    <div class="flex justify-between items-center p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                        <span class="font-medium">التوقيع الأيسر</span>
                        <span class="font-mono text-purple-600" x-text="'X:' + signatureLeft.positionX + ' Y:' + signatureLeft.positionY"></span>
                    </div>
                </template>
                <template x-if="signatureRight && signatureRight.text">
                    <div class="flex justify-between items-center p-2 bg-orange-50 dark:bg-orange-900/30 rounded-lg">
                        <span class="font-medium">التوقيع الأيمن</span>
                        <span class="font-mono text-orange-600" x-text="'X:' + signatureRight.positionX + ' Y:' + signatureRight.positionY"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- زر فتح المعاينة الكاملة -->
    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
        <a 
            href="{{ route('certificate.index') }}?preview=1&name=طالب%20تجريبي&school=مدرسة%20النصر&exam=الشهادة%20الإعدادية&score=280&max=280&percentage=100%25" 
            target="_blank"
            class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-3 px-6 rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-xl hover:shadow-blue-500/30"
        >
            <span>🔍</span>
            <span>معاينة الشهادة الكاملة</span>
        </a>
        <a 
            href="{{ route('certificate.index') }}" 
            target="_blank"
            class="flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold py-3 px-6 rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-xl hover:shadow-green-500/30"
        >
            <span>📝</span>
            <span>صفحة الشهادة الفعلية</span>
        </a>
    </div>
</div>

<script>
function certificateEditor(settings, wire) {
    return {
        // Livewire reference
        wire: wire,
        fieldPrefix: settings.fieldPrefix,
        
        // الإعدادات الأساسية
        canvasWidth: settings.canvasWidth,
        canvasHeight: settings.canvasHeight,
        bgUrl: settings.bgUrl,
        gender: settings.gender,
        isFemale: settings.isFemale,
        scale: 0.4,
        
        // الاسم
        namePositionX: settings.namePositionX,
        namePositionY: settings.namePositionY,
        nameFontSize: settings.nameFontSize,
        nameFontFamily: settings.nameFontFamily,
        primaryColor: settings.primaryColor,
        studentName: settings.studentName,
        
        // الأسطر
        lines: settings.lines,
        
        // التوقيعات
        signatureLeft: settings.signatureLeft,
        signatureRight: settings.signatureRight,
        
        // حالة التحرير
        selectedElement: null,
        selectedElementLabel: '',
        isDragging: false,
        dragStartX: 0,
        dragStartY: 0,
        elementStartX: 0,
        elementStartY: 0,
        
        // القيم الحالية للتحكم
        currentX: 0,
        currentY: 0,
        currentFontSize: 50,
        
        init() {
            // مراقبة التغييرات من Livewire
            this.$watch('scale', () => {});
        },
        
        selectElement(element, event) {
            this.selectedElement = element;
            
            if (element === 'name') {
                this.selectedElementLabel = 'الاسم الرئيسي';
                this.currentX = this.namePositionX;
                this.currentY = this.namePositionY;
                this.currentFontSize = this.nameFontSize;
            } else if (element.startsWith('line')) {
                const num = parseInt(element.replace('line', ''));
                this.selectedElementLabel = 'السطر ' + num;
                this.currentX = this.lines[num].positionX;
                this.currentY = this.lines[num].positionY;
                this.currentFontSize = this.lines[num].fontSize;
            } else if (element === 'signatureLeft') {
                this.selectedElementLabel = 'التوقيع الأيسر';
                this.currentX = this.signatureLeft.positionX;
                this.currentY = this.signatureLeft.positionY;
                this.currentFontSize = this.signatureLeft.fontSize;
            } else if (element === 'signatureRight') {
                this.selectedElementLabel = 'التوقيع الأيمن';
                this.currentX = this.signatureRight.positionX;
                this.currentY = this.signatureRight.positionY;
                this.currentFontSize = this.signatureRight.fontSize;
            }
            
            // بدء السحب
            this.isDragging = true;
            this.dragStartX = event.clientX;
            this.dragStartY = event.clientY;
            this.elementStartX = this.currentX;
            this.elementStartY = this.currentY;
        },
        
        startDrag(event) {
            // لا نفعل شيء هنا، السحب يبدأ من selectElement
        },
        
        onDrag(event) {
            if (!this.isDragging || !this.selectedElement) return;
            
            const deltaX = (event.clientX - this.dragStartX) / this.scale;
            const deltaY = (event.clientY - this.dragStartY) / this.scale;
            
            let newX = Math.round(this.elementStartX + deltaX);
            let newY = Math.round(this.elementStartY + deltaY);
            
            // حدود
            newX = Math.max(0, Math.min(this.canvasWidth, newX));
            newY = Math.max(0, Math.min(this.canvasHeight, newY));
            
            this.currentX = newX;
            this.currentY = newY;
            
            this.updatePosition();
        },
        
        stopDrag() {
            this.isDragging = false;
            // حفظ تلقائي عند انتهاء السحب
            this.syncToLivewire();
        },
        
        updatePosition() {
            if (this.selectedElement === 'name') {
                this.namePositionX = parseInt(this.currentX);
                this.namePositionY = parseInt(this.currentY);
            } else if (this.selectedElement && this.selectedElement.startsWith('line')) {
                const num = parseInt(this.selectedElement.replace('line', ''));
                this.lines[num].positionX = parseInt(this.currentX);
                this.lines[num].positionY = parseInt(this.currentY);
            } else if (this.selectedElement === 'signatureLeft') {
                this.signatureLeft.positionX = parseInt(this.currentX);
                this.signatureLeft.positionY = parseInt(this.currentY);
            } else if (this.selectedElement === 'signatureRight') {
                this.signatureRight.positionX = parseInt(this.currentX);
                this.signatureRight.positionY = parseInt(this.currentY);
            }
        },
        
        updateFontSize() {
            if (this.selectedElement === 'name') {
                this.nameFontSize = parseInt(this.currentFontSize);
            } else if (this.selectedElement && this.selectedElement.startsWith('line')) {
                const num = parseInt(this.selectedElement.replace('line', ''));
                this.lines[num].fontSize = parseInt(this.currentFontSize);
            } else if (this.selectedElement === 'signatureLeft') {
                this.signatureLeft.fontSize = parseInt(this.currentFontSize);
            } else if (this.selectedElement === 'signatureRight') {
                this.signatureRight.fontSize = parseInt(this.currentFontSize);
            }
        },
        
        // مزامنة القيم مع Livewire مباشرة
        syncToLivewire() {
            const suffix = this.fieldPrefix;
            
            try {
                // تحديث موضع الاسم
                this.wire.set(`data.name_position_x${suffix}`, this.namePositionX);
                this.wire.set(`data.name_position_y${suffix}`, this.namePositionY);
                
                // تحديث الأسطر
                for (let i = 1; i <= 6; i++) {
                    if (this.lines[i]) {
                        this.wire.set(`data.line${i}_position_x${suffix}`, this.lines[i].positionX);
                        this.wire.set(`data.line${i}_position_y${suffix}`, this.lines[i].positionY);
                        this.wire.set(`data.line${i}_font_size${suffix}`, this.lines[i].fontSize);
                    }
                }
                
                // تحديث التوقيعات (مشتركة بين الذكور والإناث)
                if (this.signatureLeft) {
                    this.wire.set('data.signature_left_position_x', this.signatureLeft.positionX);
                    this.wire.set('data.signature_left_position_y', this.signatureLeft.positionY);
                    this.wire.set('data.signature_left_font_size', this.signatureLeft.fontSize);
                }
                if (this.signatureRight) {
                    this.wire.set('data.signature_right_position_x', this.signatureRight.positionX);
                    this.wire.set('data.signature_right_position_y', this.signatureRight.positionY);
                    this.wire.set('data.signature_right_font_size', this.signatureRight.fontSize);
                }
                
                console.log('✅ تم مزامنة القيم مع Livewire');
            } catch (e) {
                console.error('خطأ في المزامنة:', e);
                // fallback to DOM manipulation
                this.applyChangesViaDOM();
            }
        },
        
        copyValues() {
            const suffix = this.isFemale ? '_female' : '';
            let text = `📋 القيم للنسخ (${this.isFemale ? 'إناث' : 'ذكور'}):\n\n`;
            
            text += `موضع الاسم:\n`;
            text += `  X: ${this.namePositionX}\n`;
            text += `  Y: ${this.namePositionY}\n\n`;
            
            for (let i = 1; i <= 6; i++) {
                if (this.lines[i] && this.lines[i].text) {
                    text += `السطر ${i}:\n`;
                    text += `  X: ${this.lines[i].positionX}\n`;
                    text += `  Y: ${this.lines[i].positionY}\n`;
                    text += `  حجم الخط: ${this.lines[i].fontSize}\n\n`;
                }
            }
            
            // التوقيعات
            if (this.signatureLeft && this.signatureLeft.text) {
                text += `التوقيع الأيسر:\n`;
                text += `  X: ${this.signatureLeft.positionX}\n`;
                text += `  Y: ${this.signatureLeft.positionY}\n`;
                text += `  حجم الخط: ${this.signatureLeft.fontSize}\n\n`;
            }
            if (this.signatureRight && this.signatureRight.text) {
                text += `التوقيع الأيمن:\n`;
                text += `  X: ${this.signatureRight.positionX}\n`;
                text += `  Y: ${this.signatureRight.positionY}\n`;
                text += `  حجم الخط: ${this.signatureRight.fontSize}\n\n`;
            }
            
            navigator.clipboard.writeText(text).then(() => {
                this.showNotification('📋 تم نسخ جميع القيم! الصقها في أي مكان للرجوع إليها.');
            });
        },
        
        applyChanges() {
            // مزامنة مع Livewire
            this.syncToLivewire();
            this.showNotification('✅ تم تطبيق التغييرات! اضغط "حفظ" أعلى الصفحة.');
        },
        
        applyChangesViaDOM() {
            // تحديث الحقول في النموذج عبر DOM
            const suffix = this.isFemale ? '_female' : '';
            let updatedCount = 0;
            
            // دالة مساعدة للبحث عن الحقل وتحديثه
            const updateField = (fieldName, value) => {
                // البحث بعدة طرق مختلفة
                const selectors = [
                    `input[wire\\:model\\.live="data.${fieldName}"]`,
                    `input[wire\\:model="data.${fieldName}"]`,
                    `input[wire\\:model\\.defer="data.${fieldName}"]`,
                    `input[wire\\:model\\.lazy="data.${fieldName}"]`,
                    `[x-model="state"][id*="${fieldName}"]`,
                    `input[id*="${fieldName}"]`,
                    `input[name*="${fieldName}"]`,
                ];
                
                for (const selector of selectors) {
                    const field = document.querySelector(selector);
                    if (field) {
                        field.value = value;
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                        
                        // محاولة تحديث Alpine.js state
                        if (field._x_model) {
                            field._x_model.set(value);
                        }
                        
                        updatedCount++;
                        console.log(`✅ Updated ${fieldName} = ${value}`);
                        return true;
                    }
                }
                console.warn(`⚠️ Field not found: ${fieldName}`);
                return false;
            };
            
            // تحديث موضع الاسم
            updateField(`name_position_x${suffix}`, this.namePositionX);
            updateField(`name_position_y${suffix}`, this.namePositionY);
            
            // تحديث الأسطر
            for (let i = 1; i <= 6; i++) {
                if (this.lines[i]) {
                    updateField(`line${i}_position_x${suffix}`, this.lines[i].positionX);
                    updateField(`line${i}_position_y${suffix}`, this.lines[i].positionY);
                    updateField(`line${i}_font_size${suffix}`, this.lines[i].fontSize);
                }
            }
        },
        
        showNotification(message, type = 'success') {
            // إنشاء عنصر الإشعار
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-amber-500';
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-[9999] ${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl transform transition-all duration-500 flex items-center gap-3 font-bold max-w-md`;
            notification.innerHTML = `<span>${message}</span>`;
            document.body.appendChild(notification);
            
            // إزالة الإشعار بعد 4 ثواني
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 500);
            }, 4000);
        }
    };
}
</script>

<style>
.certificate-editor input[type="range"] {
    -webkit-appearance: none;
    height: 8px;
    border-radius: 4px;
    background: #e5e7eb;
}
.certificate-editor input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: currentColor;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
</style>
