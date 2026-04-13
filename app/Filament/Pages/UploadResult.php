<?php

namespace App\Filament\Pages;

use App\Filament\Resources\UploadLogResource;
use App\Models\AcademicYear;
use App\Models\Country;
use App\Models\ExamType;
use App\Models\Governorate;
use App\Models\UploadLog;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class UploadResult extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'رفع/تحديث نتيجة';
    protected static ?string $title = 'إدارة النتائج الموحدة';
    protected static ?string $navigationGroup = 'النتائج';
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.pages.upload-result-unified';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // الخطوة 1: اختيار الدولة والشهادة
                    Wizard\Step::make('تحديد الشهادة')
                        ->icon('heroicon-o-academic-cap')
                        ->description('اختر الدولة ونوع الشهادة')
                        ->schema([
                            Section::make()
                                ->schema([
                                    Select::make('country_id')
                                        ->label('الدولة')
                                        ->options(Country::pluck('name_ar', 'id'))
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (Set $set) {
                                            $set('exam_type_id', null);
                                            $set('governorate_id', null);
                                        })
                                        ->prefixIcon('heroicon-o-flag')
                                        ->placeholder('اختر الدولة...'),
                                    
                                    Select::make('exam_type_id')
                                        ->label('نوع الشهادة/الامتحان')
                                        ->options(fn (Get $get) => ExamType::where('country_id', $get('country_id'))->pluck('name_ar', 'id'))
                                        ->required()
                                        ->visible(fn (Get $get) => $get('country_id'))
                                        ->live()
                                        ->afterStateUpdated(fn (Set $set) => $set('governorate_id', null))
                                        ->prefixIcon('heroicon-o-document-text')
                                        ->placeholder('اختر نوع الشهادة...'),
                                    
                                    Select::make('academic_year_id')
                                        ->label('السنة الدراسية')
                                        ->options(AcademicYear::orderBy('year', 'desc')->pluck('year', 'id'))
                                        ->required()
                                        ->prefixIcon('heroicon-o-calendar')
                                        ->default(fn () => AcademicYear::orderBy('year', 'desc')->first()?->id),
                                    
                                    // المحافظة (للنتائج غير الموحدة فقط)
                                    Select::make('governorate_id')
                                        ->label('المحافظة')
                                        ->options(function (Get $get) {
                                            $countryId = $get('country_id');
                                            if (!$countryId) return [];
                                            
                                            $governorates = Governorate::where('country_id', $countryId)
                                                ->pluck('name_ar', 'id')
                                                ->toArray();
                                            
                                            // إضافة خيار "كل المحافظات"
                                            return ['all' => '📋 كل المحافظات (تطبيق على الجميع)'] + $governorates;
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->prefixIcon('heroicon-o-map-pin')
                                        ->visible(function (Get $get) {
                                            if (!$get('country_id') || !$get('exam_type_id')) return false;
                                            
                                            $examType = ExamType::find($get('exam_type_id'));
                                            if (!$examType) return false;
                                            
                                            // إخفاء للنتائج الموحدة
                                            $code = strtolower($examType->code ?? '');
                                            if (str_contains($code, 'secondary') || str_contains($code, 'diploma')) {
                                                return false;
                                            }
                                            return true;
                                        })
                                        ->helperText('اختر محافظة معينة أو "كل المحافظات" للتطبيق على الجميع'),
                                    
                                    // نظام الثانوية (قديم/حديث)
                                    Select::make('system_type')
                                        ->label('نظام الدراسة')
                                        ->options([
                                            'old' => 'نظام قديم',
                                            'new' => 'نظام حديث',
                                        ])
                                        ->visible(function (Get $get) {
                                            $examType = ExamType::find($get('exam_type_id'));
                                            return $examType && str_contains(strtolower($examType->code ?? ''), 'secondary');
                                        }),
                                    
                                    // الفصل الدراسي (للإعدادية والابتدائية)
                                    Select::make('semester')
                                        ->label('الفصل الدراسي')
                                        ->options(\App\Models\Result::getSemesterOptions())
                                        ->default(\App\Models\Result::SEMESTER_BOTH)
                                        ->visible(function (Get $get) {
                                            $examType = ExamType::find($get('exam_type_id'));
                                            if (!$examType) return false;
                                            $code = strtolower($examType->code ?? '');
                                            return str_contains($code, 'preparatory') || str_contains($code, 'primary');
                                        })
                                        ->helperText('اختر الفصل الدراسي المناسب للملف المرفوع')
                                        ->prefixIcon('heroicon-o-calendar-days'),
                                ])
                                ->columns(2),
                        ]),

                    // الخطوة 2: اختيار طريقة العرض
                    Wizard\Step::make('طريقة العرض')
                        ->icon('heroicon-o-eye')
                        ->description('حدد كيف ستظهر النتيجة للطلاب')
                        ->schema([
                            Section::make('اختر طريقة عرض النتيجة')
                                ->description('هذا يحدد كيف سيتم عرض النتيجة للطلاب على الموقع')
                                ->schema([
                                    Radio::make('display_type')
                                        ->label('')
                                        ->options([
                                            'excel' => '📊 رفع ملف Excel (بحث برقم الجلوس)',
                                            'pdf' => '📄 رفع ملف PDF (عرض مباشر)',
                                            'embed' => '🌐 رابط خارجي / iFrame (إيفريم)',
                                            'governorate_table' => '📋 جدول المحافظات (ملف لكل محافظة)',
                                        ])
                                        ->descriptions([
                                            'excel' => 'رفع ملف Excel يحتوي على بيانات الطلاب، ويتم البحث فيه برقم الجلوس أو الاسم',
                                            'pdf' => 'رفع ملف PDF يظهر للطلاب مباشرة للتصفح أو التحميل',
                                            'embed' => 'عرض صفحة خارجية (مثل موقع الوزارة) داخل إطار iframe',
                                            'governorate_table' => 'جدول بالمحافظات، كل محافظة لها ملف نتيجة منفصل',
                                        ])
                                        ->required()
                                        ->live()
                                        ->default('excel')
                                        ->columnSpanFull(),
                                    
                                    // معلومات توضيحية
                                    Placeholder::make('display_info')
                                        ->label('')
                                        ->content(function (Get $get) {
                                            $type = $get('display_type');
                                            $info = match($type) {
                                                'excel' => '<div class="p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
                                                    <p class="font-bold text-blue-800 dark:text-blue-300 mb-2">📊 رفع ملف Excel</p>
                                                    <ul class="text-sm text-blue-700 dark:text-blue-400 list-disc list-inside space-y-1">
                                                        <li>ارفع ملف Excel يحتوي على بيانات الطلاب</li>
                                                        <li>سيتم ربط الأعمدة (رقم الجلوس، الاسم، الدرجات، إلخ)</li>
                                                        <li>الطلاب يبحثون برقم الجلوس أو الاسم</li>
                                                        <li>يمكنك رفع دفعات متعددة (محافظات مختلفة)</li>
                                                    </ul>
                                                </div>',
                                                'pdf' => '<div class="p-4 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:border-green-800">
                                                    <p class="font-bold text-green-800 dark:text-green-300 mb-2">📄 ملف PDF</p>
                                                    <ul class="text-sm text-green-700 dark:text-green-400 list-disc list-inside space-y-1">
                                                        <li>ارفع ملف PDF واحد للنتيجة الكاملة</li>
                                                        <li>سيظهر عارض PDF للطلاب مباشرة</li>
                                                        <li>مناسب للنتائج المجمعة أو الكشوف الرسمية</li>
                                                    </ul>
                                                </div>',
                                                'embed' => '<div class="p-4 bg-purple-50 border border-purple-200 rounded-lg dark:bg-purple-900/20 dark:border-purple-800">
                                                    <p class="font-bold text-purple-800 dark:text-purple-300 mb-2">🌐 رابط خارجي / iFrame</p>
                                                    <ul class="text-sm text-purple-700 dark:text-purple-400 list-disc list-inside space-y-1">
                                                        <li>أدخل رابط موقع النتيجة الرسمي</li>
                                                        <li>سيتم عرضه داخل صفحتك في إطار</li>
                                                        <li>مناسب لربط موقع الوزارة أو مصدر رسمي</li>
                                                    </ul>
                                                </div>',
                                                'governorate_table' => '<div class="p-4 bg-amber-50 border border-amber-200 rounded-lg dark:bg-amber-900/20 dark:border-amber-800">
                                                    <p class="font-bold text-amber-800 dark:text-amber-300 mb-2">📋 جدول المحافظات</p>
                                                    <ul class="text-sm text-amber-700 dark:text-amber-400 list-disc list-inside space-y-1">
                                                        <li>يظهر جدول بكل المحافظات</li>
                                                        <li>كل محافظة لها ملف نتيجة منفصل (PDF)</li>
                                                        <li>يمكنك رفع ملف كل محافظة من صفحة المحافظات</li>
                                                        <li>مناسب للشهادة الإعدادية</li>
                                                    </ul>
                                                </div>',
                                                default => '',
                                            };
                                            return new HtmlString($info);
                                        })
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // الخطوة 3: رفع الملف أو إدخال البيانات
                    Wizard\Step::make('رفع البيانات')
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->description('ارفع الملف أو أدخل البيانات')
                        ->schema([
                            // === قسم رفع Excel ===
                            Section::make('رفع ملف Excel')
                                ->description('ارفع ملف الإكسيل الذي يحتوي على بيانات الطلاب')
                                ->visible(fn (Get $get) => $get('display_type') === 'excel')
                                ->schema([
                                    TextInput::make('batch_name')
                                        ->label('اسم الدفعة / الملف')
                                        ->placeholder('مثال: نتيجة القاهرة - اليوم الأول')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull()
                                        ->helperText('اسم وصفي للملف لتمييزه عن الدفعات الأخرى'),
                                    
                                    FileUpload::make('file_path')
                                        ->label('ملف الإكسيل')
                                        ->disk('local')
                                        ->directory('results_uploads')
                                        ->visibility('private')
                                        ->acceptedFileTypes([
                                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                            'application/vnd.ms-excel',
                                            'text/csv'
                                        ])
                                        ->required()
                                        ->columnSpanFull()
                                        ->helperText('يجب أن يحتوي الملف على صف رؤوس الأعمدة (Headers). الصيغ المدعومة: xlsx, xls, csv'),
                                ]),

                            // === قسم رفع PDF ===
                            Section::make('رفع ملف PDF')
                                ->description('ارفع ملف PDF للنتيجة')
                                ->visible(fn (Get $get) => $get('display_type') === 'pdf')
                                ->schema([
                                    FileUpload::make('pdf_file')
                                        ->label('ملف PDF')
                                        ->disk('public')
                                        ->directory('exam-pdfs')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(51200) // 50MB
                                        ->required()
                                        ->columnSpanFull()
                                        ->downloadable()
                                        ->openable()
                                        ->previewable()
                                        ->helperText('الحد الأقصى 50 ميجابايت'),
                                ]),

                            // === قسم Embed/iFrame ===
                            Section::make('رابط خارجي / iFrame')
                                ->description('أدخل رابط الصفحة أو كود الـ iframe وحدد إعدادات العرض')
                                ->visible(fn (Get $get) => $get('display_type') === 'embed')
                                ->schema([
                                    Radio::make('embed_input_type')
                                        ->label('نوع الإدخال')
                                        ->options([
                                            'url' => '🔗 رابط مباشر (URL)',
                                            'code' => '📝 كود iFrame كامل',
                                        ])
                                        ->default('url')
                                        ->live()
                                        ->inline()
                                        ->columnSpanFull(),
                                    
                                    TextInput::make('embed_url')
                                        ->label('رابط الصفحة')
                                        ->url()
                                        ->placeholder('https://example.com/results')
                                        ->visible(fn (Get $get) => $get('embed_input_type') === 'url')
                                        ->required(fn (Get $get) => $get('embed_input_type') === 'url')
                                        ->live(onBlur: true)
                                        ->columnSpanFull()
                                        ->helperText('رابط صفحة النتيجة الخارجية'),
                                    
                                    Textarea::make('embed_code')
                                        ->label('كود iFrame')
                                        ->placeholder('<iframe src="https://..." width="100%" height="600"></iframe>')
                                        ->visible(fn (Get $get) => $get('embed_input_type') === 'code')
                                        ->required(fn (Get $get) => $get('embed_input_type') === 'code')
                                        ->live(onBlur: true)
                                        ->rows(4)
                                        ->columnSpanFull()
                                        ->helperText('أدخل كود iframe الكامل'),
                                    
                                    Section::make('إعدادات العرض')
                                        ->description('تخصيص مظهر وأبعاد الـ iframe')
                                        ->collapsed()
                                        ->schema([
                                            Grid::make(3)
                                                ->schema([
                                                    TextInput::make('iframe_width')
                                                        ->label('العرض')
                                                        ->placeholder('100%')
                                                        ->default('100%')
                                                        ->live(onBlur: true)
                                                        ->helperText('مثال: 100% أو 800px'),
                                                    
                                                    TextInput::make('iframe_height')
                                                        ->label('الارتفاع')
                                                        ->placeholder('600px')
                                                        ->default('600px')
                                                        ->live(onBlur: true)
                                                        ->helperText('مثال: 600px أو 80vh'),
                                                    
                                                    Select::make('iframe_position')
                                                        ->label('المحاذاة')
                                                        ->options([
                                                            'center' => 'وسط',
                                                            'left' => 'يسار',
                                                            'right' => 'يمين',
                                                        ])
                                                        ->default('center')
                                                        ->live(),
                                                ]),
                                            
                                            Grid::make(2)
                                                ->schema([
                                                    Toggle::make('iframe_scrolling')
                                                        ->label('السماح بالتمرير')
                                                        ->default(true)
                                                        ->live()
                                                        ->helperText('السماح بالتمرير داخل الإطار'),
                                                    
                                                    Toggle::make('iframe_border')
                                                        ->label('إظهار الإطار')
                                                        ->default(false)
                                                        ->live()
                                                        ->helperText('إظهار حدود حول الـ iframe'),
                                                ]),
                                        ]),
                                    
                                    // إعدادات القص المتقدمة
                                    Section::make('تحديد منطقة معينة (قص)')
                                        ->description('إظهار جزء معين من الصفحة بدلاً من عرضها كاملة - استخدم الماوس لتحديد المنطقة')
                                        ->collapsed()
                                        ->schema([
                                            Toggle::make('iframe_crop_enabled')
                                                ->label('تفعيل تحديد المنطقة')
                                                ->helperText('قم بتفعيل هذا الخيار لإظهار جزء محدد من الصفحة')
                                                ->default(false)
                                                ->live()
                                                ->columnSpanFull(),
                                            
                                            // أداة القص التفاعلية بالماوس
                                            ViewField::make('crop_tool')
                                                ->view('filament.components.iframe-crop-tool')
                                                ->viewData(fn (Get $get) => [
                                                    'embedUrl' => $get('embed_url') ?? '',
                                                    'embedCode' => $get('embed_code') ?? '',
                                                    'inputType' => $get('embed_input_type') ?? 'url',
                                                    'cropEnabled' => $get('iframe_crop_enabled') ?? false,
                                                ])
                                                ->visible(fn (Get $get) => $get('embed_url') || $get('embed_code'))
                                                ->dehydrated(false)
                                                ->columnSpanFull(),
                                            
                                            // حقول القص (مخفية - يتم تحديثها من أداة القص)
                                            Grid::make(3)
                                                ->visible(fn (Get $get) => $get('iframe_crop_enabled'))
                                                ->schema([
                                                    TextInput::make('iframe_crop_top')
                                                        ->label('من الأعلى (بكسل)')
                                                        ->numeric()
                                                        ->placeholder('0')
                                                        ->default('0')
                                                        ->live(onBlur: true)
                                                        ->suffix('px')
                                                        ->helperText('يمكنك الكتابة يدوياً أيضاً'),
                                                    
                                                    TextInput::make('iframe_crop_left')
                                                        ->label('من اليسار (بكسل)')
                                                        ->numeric()
                                                        ->placeholder('0')
                                                        ->default('0')
                                                        ->live(onBlur: true)
                                                        ->suffix('px')
                                                        ->helperText('يمكنك الكتابة يدوياً أيضاً'),
                                                    
                                                    TextInput::make('iframe_zoom')
                                                        ->label('نسبة التكبير')
                                                        ->numeric()
                                                        ->placeholder('100')
                                                        ->default('100')
                                                        ->live(onBlur: true)
                                                        ->suffix('%')
                                                        ->helperText('100 = الحجم الطبيعي'),
                                                ]),
                                        ]),
                                    
                                    // معاينة الـ iframe
                                    Section::make('معاينة')
                                        ->description('هكذا سيظهر الـ iframe للطلاب')
                                        ->schema([
                                            Placeholder::make('iframe_preview')
                                                ->label('')
                                                ->content(function (Get $get) {
                                                    $inputType = $get('embed_input_type') ?? 'url';
                                                    $url = $get('embed_url');
                                                    $code = $get('embed_code');
                                                    $width = $get('iframe_width') ?: '100%';
                                                    $height = $get('iframe_height') ?: '600px';
                                                    $position = $get('iframe_position') ?: 'center';
                                                    $scrolling = $get('iframe_scrolling') ?? true;
                                                    $border = $get('iframe_border') ?? false;
                                                    
                                                    // إعدادات القص
                                                    $cropEnabled = $get('iframe_crop_enabled') ?? false;
                                                    $cropTop = intval($get('iframe_crop_top') ?: 0);
                                                    $cropLeft = intval($get('iframe_crop_left') ?: 0);
                                                    $zoom = intval($get('iframe_zoom') ?: 100);
                                                    
                                                    // تحديد مصدر الـ iframe
                                                    $src = '';
                                                    if ($inputType === 'url' && $url) {
                                                        $src = $url;
                                                    } elseif ($inputType === 'code' && $code) {
                                                        // استخراج الـ src من الكود
                                                        if (preg_match('/src=["\']([^"\']+)["\']/', $code, $matches)) {
                                                            $src = $matches[1];
                                                        }
                                                    }
                                                    
                                                    if (empty($src)) {
                                                        return new HtmlString('
                                                            <div class="p-8 bg-gray-100 dark:bg-gray-700 rounded-lg text-center">
                                                                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                                                                </svg>
                                                                <p class="mt-4 text-gray-500 dark:text-gray-400">أدخل رابط أو كود لمعاينة الـ iframe</p>
                                                            </div>
                                                        ');
                                                    }
                                                    
                                                    // تحديد المحاذاة
                                                    $alignStyle = match($position) {
                                                        'left' => 'margin-right: auto;',
                                                        'right' => 'margin-left: auto;',
                                                        default => 'margin: 0 auto;',
                                                    };
                                                    
                                                    $scrollingAttr = $scrolling ? 'yes' : 'no';
                                                    $borderStyle = $border ? 'border: 2px solid #e5e7eb;' : 'border: none;';
                                                    
                                                    // تقليل الارتفاع للمعاينة
                                                    $previewHeight = '400px';
                                                    
                                                    // إعداد CSS للقص
                                                    $iframeStyle = 'width: 100%; height: ' . $previewHeight . '; ' . $borderStyle . ' border-radius: 8px;';
                                                    $containerStyle = $alignStyle . ' max-width: ' . htmlspecialchars($width) . ';';
                                                    $wrapperStyle = 'overflow: hidden; position: relative;';
                                                    
                                                    if ($cropEnabled && ($cropTop > 0 || $cropLeft > 0 || $zoom != 100)) {
                                                        $scale = $zoom / 100;
                                                        $iframeStyle = 'position: absolute; top: -' . $cropTop . 'px; left: -' . $cropLeft . 'px; '
                                                            . 'width: calc(100% + ' . $cropLeft . 'px); '
                                                            . 'height: calc(' . $previewHeight . ' + ' . $cropTop . 'px); '
                                                            . 'transform: scale(' . $scale . '); transform-origin: top left; '
                                                            . $borderStyle;
                                                        $wrapperStyle = 'overflow: hidden; position: relative; height: ' . $previewHeight . '; border-radius: 8px;';
                                                        
                                                        $cropInfo = '<div class="mt-2 p-2 bg-purple-100 dark:bg-purple-900/30 rounded text-xs text-purple-700 dark:text-purple-300">
                                                            ✂️ القص مفعّل: إخفاء ' . $cropTop . 'px من الأعلى، ' . $cropLeft . 'px من اليسار، التكبير: ' . $zoom . '%
                                                        </div>';
                                                    } else {
                                                        $cropInfo = '';
                                                    }
                                                    
                                                    return new HtmlString('
                                                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                                            <div class="flex items-center justify-between mb-3">
                                                                <span class="text-xs text-gray-500 dark:text-gray-400">معاينة (الارتفاع مصغر)</span>
                                                                <div class="flex gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                                    <span>العرض: ' . htmlspecialchars($width) . '</span>
                                                                    <span>|</span>
                                                                    <span>الارتفاع: ' . htmlspecialchars($height) . '</span>
                                                                </div>
                                                            </div>
                                                            <div style="' . $containerStyle . '">
                                                                <div style="' . $wrapperStyle . '">
                                                                    <iframe 
                                                                        src="' . htmlspecialchars($src) . '" 
                                                                        style="' . $iframeStyle . '"
                                                                        scrolling="' . $scrollingAttr . '"
                                                                        loading="lazy"
                                                                        allowfullscreen
                                                                    ></iframe>
                                                                </div>
                                                            </div>
                                                            ' . $cropInfo . '
                                                        </div>
                                                    ');
                                                })
                                                ->columnSpanFull(),
                                        ]),
                                ]),

                            // === قسم جدول المحافظات ===
                            Section::make('إعداد جدول المحافظات')
                                ->description('حدد المحافظات التي ظهرت نتيجتها وارفع ملف PDF لكل محافظة')
                                ->visible(fn (Get $get) => $get('display_type') === 'governorate_table')
                                ->schema([
                                    Placeholder::make('gov_table_info')
                                        ->label('')
                                        ->content(new HtmlString('
                                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg dark:bg-amber-900/20 dark:border-amber-800">
                                                <p class="font-bold text-amber-800 dark:text-amber-300 mb-2">📋 كيفية استخدام جدول المحافظات:</p>
                                                <ul class="text-sm text-amber-700 dark:text-amber-400 list-disc list-inside space-y-1">
                                                    <li>فعّل المحافظات التي ظهرت نتيجتها بالضغط على زر التفعيل</li>
                                                    <li>ارفع ملف PDF لكل محافظة من الأسفل</li>
                                                    <li>سيظهر للطلاب جدول بالمحافظات مع زر تحميل للمحافظات المتاحة</li>
                                                </ul>
                                            </div>
                                        '))
                                        ->columnSpanFull(),
                                    
                                    // أزرار سريعة
                                    Grid::make(2)
                                        ->schema([
                                            Toggle::make('declare_all_governorates')
                                                ->label('اعتماد جميع المحافظات')
                                                ->helperText('تفعيل حالة "معتمدة" لجميع المحافظات')
                                                ->live()
                                                ->afterStateUpdated(function ($state, Get $get) {
                                                    if ($state) {
                                                        $countryId = $get('country_id');
                                                        if ($countryId) {
                                                            Governorate::where('country_id', $countryId)
                                                                ->update(['is_declared' => true]);
                                                        }
                                                    }
                                                }),
                                            
                                            Toggle::make('undeclare_all_governorates')
                                                ->label('إلغاء اعتماد جميع المحافظات')
                                                ->helperText('إلغاء حالة "معتمدة" لجميع المحافظات')
                                                ->live()
                                                ->afterStateUpdated(function ($state, Get $get) {
                                                    if ($state) {
                                                        $countryId = $get('country_id');
                                                        if ($countryId) {
                                                            Governorate::where('country_id', $countryId)
                                                                ->update(['is_declared' => false]);
                                                        }
                                                    }
                                                }),
                                        ]),
                                    
                                    // جدول المحافظات مع رفع الملفات
                                    Placeholder::make('governorates_upload_section')
                                        ->label('ملفات المحافظات')
                                        ->content(function (Get $get) {
                                            $countryId = $get('country_id');
                                            if (!$countryId) {
                                                return new HtmlString('<p class="text-gray-500">اختر الدولة أولاً لعرض المحافظات</p>');
                                            }
                                            
                                            $governorates = Governorate::where('country_id', $countryId)
                                                ->orderBy('name_ar')
                                                ->get();
                                            
                                            if ($governorates->isEmpty()) {
                                                return new HtmlString('<p class="text-gray-500">لا توجد محافظات لهذه الدولة</p>');
                                            }
                                            
                                            $declared = $governorates->where('is_declared', true)->count();
                                            $withPdf = $governorates->filter(fn($g) => $g->hasResultPdf())->count();
                                            
                                            return new HtmlString('
                                                <div class="grid grid-cols-3 gap-4 mb-4">
                                                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg text-center">
                                                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">' . $governorates->count() . '</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">إجمالي المحافظات</p>
                                                    </div>
                                                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg text-center">
                                                        <p class="text-2xl font-bold text-green-700 dark:text-green-400">' . $declared . '</p>
                                                        <p class="text-xs text-green-600 dark:text-green-500">معتمدة</p>
                                                    </div>
                                                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-center">
                                                        <p class="text-2xl font-bold text-blue-700 dark:text-blue-400">' . $withPdf . '</p>
                                                        <p class="text-xs text-blue-600 dark:text-blue-500">بها ملفات</p>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                    👇 اختر المحافظات وارفع ملفاتها من القائمة أدناه
                                                </p>
                                            ');
                                        })
                                        ->columnSpanFull(),
                                    
                                    // Repeater للمحافظات
                                    \Filament\Forms\Components\Repeater::make('governorate_files')
                                        ->label('رفع ملفات المحافظات')
                                        ->schema([
                                            Select::make('governorate_id')
                                                ->label('المحافظة')
                                                ->options(fn (Get $get) => 
                                                    Governorate::where('country_id', $get('../../country_id'))
                                                        ->orderBy('name_ar')
                                                        ->pluck('name_ar', 'id')
                                                )
                                                ->required()
                                                ->searchable()
                                                ->live()
                                                ->afterStateUpdated(function ($state, Set $set) {
                                                    if ($state) {
                                                        $gov = Governorate::find($state);
                                                        if ($gov) {
                                                            $set('is_declared', $gov->is_declared);
                                                            $set('current_pdf', $gov->result_pdf_path);
                                                        }
                                                    }
                                                }),
                                            
                                            Toggle::make('is_declared')
                                                ->label('النتيجة معتمدة')
                                                ->helperText('تفعيل لإظهار زر التحميل للطلاب')
                                                ->default(true),
                                            
                                            FileUpload::make('pdf_file')
                                                ->label('ملف PDF')
                                                ->disk('public')
                                                ->directory('governorate-results')
                                                ->acceptedFileTypes(['application/pdf'])
                                                ->maxSize(51200)
                                                ->downloadable()
                                                ->openable()
                                                ->helperText('ارفع ملف PDF للنتيجة (الحد الأقصى 50MB)'),
                                            
                                            Placeholder::make('current_pdf')
                                                ->label('')
                                                ->content(fn (Get $get) => $get('current_pdf') 
                                                    ? new HtmlString('<span class="text-green-600 text-sm">✓ يوجد ملف مرفوع سابقاً</span>')
                                                    : new HtmlString('<span class="text-gray-400 text-sm">لا يوجد ملف</span>')
                                                ),
                                        ])
                                        ->columns(4)
                                        ->addActionLabel('➕ إضافة محافظة')
                                        ->reorderable(false)
                                        ->defaultItems(0)
                                        ->collapsible()
                                        ->columnSpanFull(),
                                    
                                    Toggle::make('confirm_gov_table')
                                        ->label('تفعيل عرض جدول المحافظات للطلاب')
                                        ->helperText('عند التفعيل، سيظهر للطلاب جدول بالمحافظات مع إمكانية تحميل النتيجة')
                                        ->default(true),
                                ]),
                        ]),
                ])
                ->skippable(false)
                ->persistStepInQueryString()
                ->submitAction(new HtmlString('
                    <button type="submit" class="fi-btn fi-btn-size-md fi-btn-color-primary gap-1.5 px-4 py-2.5 text-sm font-semibold shadow-sm rounded-lg bg-primary-600 text-white hover:bg-primary-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        حفظ وتطبيق
                    </button>
                ')),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $displayType = $data['display_type'];
        $examTypeId = $data['exam_type_id'];
        
        $examType = ExamType::find($examTypeId);
        
        if (!$examType) {
            Notification::make()
                ->title('خطأ')
                ->body('لم يتم العثور على نوع الشهادة')
                ->danger()
                ->send();
            return;
        }

        // تحديث نوع خدمة العرض في ExamType
        $serviceType = match($displayType) {
            'excel' => 'search',
            'pdf' => 'pdf',
            'embed' => 'embed',
            'governorate_table' => 'governorate_table',
            default => 'search',
        };

        // === معالجة حسب نوع العرض ===
        
        if ($displayType === 'excel') {
            // رفع ملف Excel - نفس الكود القديم
            $headers = [];
            try {
                if (isset($data['file_path'])) {
                    $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['file_path']);
                    $service = new \App\Services\FileImportService();
                    $headers = $service->getHeaders($fullPath);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to extract headers on upload: ' . $e->getMessage());
            }

            $uploadLog = UploadLog::create([
                'user_id' => Auth::id(),
                'batch_name' => $data['batch_name'],
                'academic_year_id' => $data['academic_year_id'],
                'exam_type_id' => $examTypeId,
                'system_type' => $data['system_type'] ?? null,
                'semester' => $data['semester'] ?? 0,
                'governorate_id' => ($data['governorate_id'] ?? null) === 'all' ? null : ($data['governorate_id'] ?? null),
                'file_path' => $data['file_path'],
                'filename' => basename($data['file_path']),
                'file_type' => pathinfo($data['file_path'], PATHINFO_EXTENSION),
                'status' => 'pending',
                'upload_type' => UploadLog::TYPE_EXCEL,
                'records_count' => 0,
                'mapping_data' => ['_headers' => $headers],
            ]);

            // تحديث ExamType
            $examType->update(['result_service_type' => $serviceType]);

            Notification::make()
                ->title('تم رفع الملف بنجاح')
                ->body('يرجى الآن تعيين الأعمدة واعتماد الملف.')
                ->success()
                ->send();

            // التوجيه لصفحة ربط الأعمدة
            $this->redirect(UploadLogResource::getUrl('edit', ['record' => $uploadLog]));
            return;
        }

        if ($displayType === 'pdf') {
            // رفع ملف PDF
            $examType->update([
                'result_service_type' => $serviceType,
                'pdf_file_path' => $data['pdf_file'],
            ]);

            // تسجيل في سجلات الرفع
            UploadLog::create([
                'user_id' => Auth::id(),
                'batch_name' => 'ملف PDF - ' . $examType->name_ar,
                'academic_year_id' => $data['academic_year_id'],
                'exam_type_id' => $examTypeId,
                'system_type' => $data['system_type'] ?? null,
                'semester' => $data['semester'] ?? 0,
                'governorate_id' => ($data['governorate_id'] ?? null) === 'all' ? null : ($data['governorate_id'] ?? null),
                'filename' => basename($data['pdf_file']),
                'file_path' => $data['pdf_file'],
                'file_type' => 'pdf',
                'status' => 'completed',
                'upload_type' => UploadLog::TYPE_PDF,
                'records_count' => 1,
                'successful_rows' => 1,
                'extra_data' => [
                    'pdf_file' => $data['pdf_file'],
                ],
            ]);

            Notification::make()
                ->title('تم رفع ملف PDF بنجاح')
                ->body('النتيجة الآن متاحة للطلاب بتنسيق PDF')
                ->success()
                ->send();

            $this->redirect('/dashboard');
            return;
        }

        if ($displayType === 'embed') {
            // رابط خارجي / iFrame
            $inputType = $data['embed_input_type'] ?? 'url';
            
            // تحديد الكود النهائي
            if ($inputType === 'url' && !empty($data['embed_url'])) {
                $embedCode = $data['embed_url'];
            } else {
                $embedCode = $data['embed_code'] ?? '';
            }
            
            $iframeSettings = [
                'input_type' => $inputType,
                'embed_url' => $data['embed_url'] ?? null,
                'embed_code' => $data['embed_code'] ?? null,
                'width' => $data['iframe_width'] ?? '100%',
                'height' => $data['iframe_height'] ?? '600px',
                'position' => $data['iframe_position'] ?? 'center',
                'scrolling' => $data['iframe_scrolling'] ?? true,
                'border' => $data['iframe_border'] ?? false,
                'crop_enabled' => $data['iframe_crop_enabled'] ?? false,
                'crop_top' => $data['iframe_crop_top'] ?? '0',
                'crop_left' => $data['iframe_crop_left'] ?? '0',
                'zoom' => $data['iframe_zoom'] ?? '100',
            ];
            
            $examType->update([
                'result_service_type' => $serviceType,
                'embed_code' => $embedCode,
                'iframe_width' => $iframeSettings['width'],
                'iframe_height' => $iframeSettings['height'],
                'iframe_position' => $iframeSettings['position'],
                'iframe_scrolling' => $iframeSettings['scrolling'],
                'iframe_border' => $iframeSettings['border'],
                'iframe_crop_enabled' => $iframeSettings['crop_enabled'],
                'iframe_crop_top' => $iframeSettings['crop_top'],
                'iframe_crop_left' => $iframeSettings['crop_left'],
                'iframe_zoom' => $iframeSettings['zoom'],
            ]);

            // تسجيل في سجلات الرفع
            UploadLog::create([
                'user_id' => Auth::id(),
                'batch_name' => 'رابط خارجي - ' . $examType->name_ar,
                'academic_year_id' => $data['academic_year_id'],
                'exam_type_id' => $examTypeId,
                'system_type' => $data['system_type'] ?? null,
                'semester' => $data['semester'] ?? 0,
                'governorate_id' => ($data['governorate_id'] ?? null) === 'all' ? null : ($data['governorate_id'] ?? null),
                'filename' => $inputType === 'url' ? ($data['embed_url'] ?? 'embed') : 'iframe-code',
                'file_type' => 'embed',
                'status' => 'completed',
                'upload_type' => UploadLog::TYPE_EMBED,
                'records_count' => 1,
                'successful_rows' => 1,
                'extra_data' => $iframeSettings,
            ]);

            Notification::make()
                ->title('تم حفظ رابط الإيفريم بنجاح')
                ->body('النتيجة الآن تعرض من المصدر الخارجي')
                ->success()
                ->send();

            $this->redirect('/dashboard');
            return;
        }

        if ($displayType === 'governorate_table') {
            // جدول المحافظات
            $examType->update([
                'result_service_type' => $serviceType,
            ]);
            
            // معالجة ملفات المحافظات
            $governorateFiles = $data['governorate_files'] ?? [];
            $uploadedCount = 0;
            $governoratesData = [];
            
            foreach ($governorateFiles as $item) {
                $govId = $item['governorate_id'] ?? null;
                if (!$govId) continue;
                
                $governorate = Governorate::find($govId);
                if (!$governorate) continue;
                
                $updateData = [
                    'is_declared' => $item['is_declared'] ?? false,
                ];
                
                // إذا تم رفع ملف جديد
                if (!empty($item['pdf_file'])) {
                    $updateData['result_pdf_path'] = $item['pdf_file'];
                    $uploadedCount++;
                    
                    // تسجيل كل ملف محافظة
                    UploadLog::create([
                        'user_id' => Auth::id(),
                        'batch_name' => 'ملف محافظة - ' . $governorate->name_ar,
                        'academic_year_id' => $data['academic_year_id'],
                        'exam_type_id' => $examTypeId,
                        'governorate_id' => $govId,
                        'filename' => basename($item['pdf_file']),
                        'file_path' => $item['pdf_file'],
                        'file_type' => 'pdf',
                        'status' => 'completed',
                        'upload_type' => UploadLog::TYPE_GOVERNORATE_FILE,
                        'records_count' => 1,
                        'successful_rows' => 1,
                        'extra_data' => [
                            'pdf_file' => $item['pdf_file'],
                            'is_declared' => $updateData['is_declared'],
                        ],
                    ]);
                }
                
                $governorate->update($updateData);
                $governoratesData[$govId] = $updateData;
            }

            // تسجيل تفعيل جدول المحافظات
            UploadLog::create([
                'user_id' => Auth::id(),
                'batch_name' => 'تفعيل جدول المحافظات - ' . $examType->name_ar,
                'academic_year_id' => $data['academic_year_id'],
                'exam_type_id' => $examTypeId,
                'file_type' => 'governorate_table',
                'status' => 'completed',
                'upload_type' => UploadLog::TYPE_GOVERNORATE_TABLE,
                'records_count' => count($governoratesData),
                'successful_rows' => count($governoratesData),
                'extra_data' => [
                    'governorates' => $governoratesData,
                    'files_uploaded' => $uploadedCount,
                ],
            ]);

            $message = 'تم تفعيل جدول المحافظات';
            if ($uploadedCount > 0) {
                $message .= " وتم رفع $uploadedCount ملف/ملفات";
            }

            Notification::make()
                ->title('تم الحفظ بنجاح')
                ->body($message)
                ->success()
                ->send();

            // البقاء في نفس الصفحة
            $this->redirect('/dashboard/upload-result');
            return;
        }
    }
}
