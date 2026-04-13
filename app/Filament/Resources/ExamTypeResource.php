<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamTypeResource\Pages;
use App\Filament\Resources\ExamTypeResource\RelationManagers;
use App\Models\ExamType;
use App\Models\Governorate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamTypeResource extends Resource
{
    protected static ?string $model = ExamType::class;

    protected static ?string $modelLabel = 'نوع الشهادة';
    protected static ?string $pluralModelLabel = 'أنواع الشهادات';
    protected static ?string $navigationLabel = 'أنواع الشهادات';
    protected static ?string $navigationGroup = 'إدارة البيانات';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات أساسية')
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->label('الدولة')
                            ->relationship('country', 'name_ar')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->live(),
                        Forms\Components\TextInput::make('code')
                            ->label('الكود')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name_ar')
                            ->label('اسم الشهادة (عربي)')
                            ->placeholder('مثال: الشهادة الإعدادية')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),
                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم الشهادة (إنجليزي)')
                            ->placeholder('Example: Middle School Certificate')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                // صندوق المحتوى - في البداية للوصول السهل
                Forms\Components\Section::make('📝 صندوق المحتوى')
                    ->description('محتوى يظهر في صفحة الشهادة لتحسين SEO وتجربة المستخدم - يدعم الجداول والترويسات')
                    ->schema([
                        Forms\Components\Toggle::make('show_content_section')
                            ->label('تفعيل صندوق المحتوى')
                            ->helperText('إظهار/إخفاء صندوق المحتوى في صفحة الشهادة')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('content_title')
                            ->label('عنوان الصندوق')
                            ->placeholder('معلومات عن الشهادة')
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('content_intro')
                            ->label('مقدمة قصيرة')
                            ->placeholder('نبذة مختصرة...')
                            ->rows(2)
                            ->columnSpanFull(),
                        
                        Forms\Components\RichEditor::make('content_body')
                            ->label('المحتوى الرئيسي')
                            ->placeholder('اكتب محتوى عن هذه الشهادة...')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'orderedList',
                                'bulletList',
                                'h2',
                                'h3',
                                'blockquote',
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('content_table_html')
                            ->label('🔹 كود HTML للجدول (اختياري)')
                            ->placeholder('<table>
  <thead>
    <tr>
      <th>العمود 1</th>
      <th>العمود 2</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>بيانات 1</td>
      <td>بيانات 2</td>
    </tr>
  </tbody>
</table>')
                            ->rows(8)
                            ->helperText('الصق كود HTML للجدول هنا. سيظهر بعد المحتوى الرئيسي بتنسيق جميل.')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('show_popular_searches')
                            ->label('تفعيل كلمات البحث الشائعة')
                            ->helperText('إظهار/إخفاء بوكس كلمات البحث الشائعة أسفل مربع البحث')
                            ->default(true)
                            ->live(),

                        Forms\Components\TagsInput::make('popular_searches')
                            ->label('كلمات البحث الشائعة')
                            ->placeholder('اضغط Enter بعد كل كلمة')
                            ->helperText('أضف أعلى 4-6 كلمات بحث شائعة من جوجل لهذه الشهادة')
                            ->visible(fn (Forms\Get $get) => $get('show_popular_searches'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('حالة اعتماد النتيجة')
                    ->description('عند تفعيل اعتماد النتيجة، سيتم تعبئة نص الإعلان تلقائياً')
                    ->schema([
                        Forms\Components\Toggle::make('is_result_approved')
                            ->label('تم اعتماد النتيجة رسمياً')
                            ->helperText('عند التفعيل، سيظهر بانر الإعلان عن النتيجة للطلاب')
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, bool $state, Forms\Get $get) {
                                if ($state) {
                                    // تحديث النص تلقائياً عند تفعيل الاعتماد
                                    $examName = $get('name_ar') ?? 'النتيجة';
                                    // Get academic year from country
                                    $countryId = $get('country_id');
                                    $academicYear = '';
                                    if ($countryId) {
                                        $country = \App\Models\Country::find($countryId);
                                        if ($country && $country->academic_year) {
                                            $academicYear = ' ' . $country->academic_year;
                                        }
                                    }
                                    $defaultText = "تم اعتماد نتيجة {$examName}{$academicYear} رسمياً\nالنتيجة متاحة الآن - يمكنك البحث برقم الجلوس";
                                    $set('result_announcement_text', $defaultText);
                                }
                            }),
                        
                        Forms\Components\Textarea::make('result_announcement_text')
                            ->label('نص الإعلان')
                            ->helperText('النص الذي سيظهر للطلاب عند اعتماد النتيجة (يمكنك تعديله)')
                            ->rows(3)
                            ->placeholder("تم اعتماد النتيجة رسمياً\nالنتيجة متاحة الآن - يمكنك البحث برقم الجلوس")
                            ->visible(fn (Forms\Get $get) => $get('is_result_approved'))
                            ->columnSpanFull(),
                        
                        Forms\Components\Placeholder::make('approval_status_hint')
                            ->label('')
                            ->content(fn (Forms\Get $get) => $get('is_result_approved') 
                                ? '✅ النتيجة معتمدة - سيظهر بانر الإعلان للطلاب'
                                : '⏳ النتيجة غير معتمدة بعد')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columns(2),

                Forms\Components\Section::make('إعدادات حساب الحالة')
                    ->description('حدد المجموع الكلي وحدود النجاح لحساب حالة الطالب تلقائياً')
                    ->schema([
                        Forms\Components\Toggle::make('auto_calculate_status')
                            ->label('حساب الحالة تلقائياً')
                            ->helperText('عند التفعيل، سيتم حساب حالة الطالب (ناجح/راسب/دور ثاني) تلقائياً بناءً على المجموع')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),
                        
                        // Default settings (for full year / both semesters)
                        Forms\Components\Fieldset::make('إعدادات الترمين (المجموع الكلي)')
                            ->schema([
                                Forms\Components\TextInput::make('total_score')
                                    ->label('المجموع الكلي')
                                    ->helperText('مجموع الترمين معاً (مثال: 280 للإعدادية)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                                
                                Forms\Components\TextInput::make('passing_score')
                                    ->label('حد النجاح')
                                    ->helperText('الحد الأدنى للنجاح (50%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                                
                                Forms\Components\TextInput::make('second_round_threshold')
                                    ->label('حد الدور الثاني')
                                    ->helperText('اختياري')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                            ])
                            ->columns(3)
                            ->visible(fn (Forms\Get $get) => $get('auto_calculate_status')),

                        Forms\Components\Toggle::make('has_semester_settings')
                            ->label('تفعيل إعدادات منفصلة لكل فصل دراسي')
                            ->helperText('فعّل هذا الخيار إذا كنت تريد تحديد إعدادات مختلفة لكل ترم (مثلاً: ترم أول 140، ترم ثاني 140)')
                            ->live()
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get) => $get('auto_calculate_status')),
                        
                        // Semester 1 settings
                        Forms\Components\Fieldset::make('إعدادات الترم الأول')
                            ->schema([
                                Forms\Components\TextInput::make('semester1_total_score')
                                    ->label('المجموع الكلي')
                                    ->helperText('مثال: 140')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                                
                                Forms\Components\TextInput::make('semester1_passing_score')
                                    ->label('حد النجاح')
                                    ->helperText('50% من المجموع')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                                
                                Forms\Components\TextInput::make('semester1_second_round')
                                    ->label('حد الدور الثاني')
                                    ->helperText('اختياري')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                            ])
                            ->columns(3)
                            ->visible(fn (Forms\Get $get) => $get('auto_calculate_status') && $get('has_semester_settings')),
                        
                        // Semester 2 settings
                        Forms\Components\Fieldset::make('إعدادات الترم الثاني')
                            ->schema([
                                Forms\Components\TextInput::make('semester2_total_score')
                                    ->label('المجموع الكلي')
                                    ->helperText('مثال: 140')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                                
                                Forms\Components\TextInput::make('semester2_passing_score')
                                    ->label('حد النجاح')
                                    ->helperText('50% من المجموع')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                                
                                Forms\Components\TextInput::make('semester2_second_round')
                                    ->label('حد الدور الثاني')
                                    ->helperText('اختياري')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                            ])
                            ->columns(3)
                            ->visible(fn (Forms\Get $get) => $get('auto_calculate_status') && $get('has_semester_settings')),

                        // System Type Settings (Old/New for Secondary)
                        Forms\Components\Toggle::make('has_system_type_settings')
                            ->label('تفعيل إعدادات نظام الدراسة (قديم/حديث)')
                            ->helperText('للثانوية العامة: فعّل هذا الخيار لتحديد إعدادات مختلفة للنظام القديم والحديث')
                            ->live()
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get) => $get('auto_calculate_status')),

                        // Old System settings
                        Forms\Components\Fieldset::make('إعدادات النظام القديم')
                            ->schema([
                                Forms\Components\TextInput::make('old_system_total_score')
                                    ->label('المجموع الكلي')
                                    ->helperText('مثال: 410')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                                
                                Forms\Components\TextInput::make('old_system_passing_score')
                                    ->label('حد النجاح')
                                    ->helperText('50% من المجموع')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                                
                                Forms\Components\TextInput::make('old_system_second_round')
                                    ->label('حد الدور الثاني')
                                    ->helperText('اختياري')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                            ])
                            ->columns(3)
                            ->visible(fn (Forms\Get $get) => $get('auto_calculate_status') && $get('has_system_type_settings')),

                        // New System settings
                        Forms\Components\Fieldset::make('إعدادات النظام الحديث')
                            ->schema([
                                Forms\Components\TextInput::make('new_system_total_score')
                                    ->label('المجموع الكلي')
                                    ->helperText('مثال: 410')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                                
                                Forms\Components\TextInput::make('new_system_passing_score')
                                    ->label('حد النجاح')
                                    ->helperText('50% من المجموع')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                                
                                Forms\Components\TextInput::make('new_system_second_round')
                                    ->label('حد الدور الثاني')
                                    ->helperText('اختياري')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('درجة'),
                            ])
                            ->columns(3)
                            ->visible(fn (Forms\Get $get) => $get('auto_calculate_status') && $get('has_system_type_settings')),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('المواد المستثناة من المجموع')
                    ->description('المواد التي لا تُضاف لحساب المجموع الكلي (مثل: التربية الدينية، التربية الوطنية، الحاسب الآلي...)')
                    ->schema([
                        Forms\Components\TagsInput::make('excluded_subjects')
                            ->label('أسماء المواد المستثناة')
                            ->helperText('اكتب اسم المادة ثم اضغط Enter لإضافتها. يمكنك إضافة أكثر من مادة.')
                            ->placeholder('مثال: التربية الدينية')
                            ->separator(',')
                            ->splitKeys(['Tab', ',', 'Enter'])
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('علامات الغياب')
                    ->description('الكلمات التي تدل على غياب الطالب في المادة. عند وجودها تُحتسب الدرجة بصفر.')
                    ->schema([
                        Forms\Components\TagsInput::make('absent_markers')
                            ->label('كلمات/رموز الغياب')
                            ->helperText('اكتب الكلمة أو الرمز ثم اضغط Enter. مثال: غ، غائب، محروم، -')
                            ->placeholder('مثال: غ')
                            ->separator(',')
                            ->splitKeys(['Tab', ',', 'Enter'])
                            ->default(\App\Models\ExamType::DEFAULT_ABSENT_MARKERS)
                            ->columnSpanFull(),
                        
                        Forms\Components\Placeholder::make('default_markers_info')
                            ->label('الكلمات الافتراضية')
                            ->content('إذا تركت الحقل فارغاً، سيتم استخدام القيم الافتراضية: غ، غ.، غائب، غائبة، غياب، غایب، -، --، ---، absent، abs، محروم، مغترب')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('نوع خدمة عرض النتيجة')
                    ->description('اختر طريقة عرض النتيجة للطلاب')
                    ->schema([
                        Forms\Components\Radio::make('result_service_type')
                            ->label('نوع الخدمة')
                            ->options(function (Forms\Get $get) {
                                $options = [
                                    'search' => '🔍 البحث برقم الجلوس والاسم (الافتراضي)',
                                    'embed' => '🌐 إيفريم خارجي (Embed/iFrame)',
                                    'pdf' => '📄 عرض ملف PDF',
                                ];
                                
                                // إضافة خيار جدول المحافظات فقط للنتائج غير الموحدة
                                $countryId = $get('country_id');
                                $code = $get('code') ?? '';
                                $nameAr = $get('name_ar') ?? '';
                                
                                // التحقق من أنها ليست نتيجة موحدة
                                $unifiedTypes = ['diploma', 'thanaweya', 'baccalaureate', 'thanawya', 'secondary', 'ثانوية', 'دبلوم', 'بكالوريا'];
                                $codeOrName = strtolower($code . ' ' . $nameAr);
                                
                                $isUnified = false;
                                foreach ($unifiedTypes as $type) {
                                    if (str_contains($codeOrName, $type)) {
                                        $isUnified = true;
                                        break;
                                    }
                                }
                                
                                if (!$isUnified) {
                                    $options['governorate_table'] = '📋 جدول المحافظات (ملف لكل محافظة)';
                                }
                                
                                return $options;
                            })
                            ->descriptions([
                                'search' => 'يتم البحث في قاعدة البيانات المحلية برقم الجلوس أو الاسم',
                                'embed' => 'عرض صفحة خارجية داخل إطار (iframe) - مثل موقع وزارة التربية',
                                'pdf' => 'رفع ملف PDF يمكن للطلاب تصفحه مباشرة',
                                'governorate_table' => 'جدول يعرض المحافظات مع حالة النتيجة وزر تحميل لكل محافظة',
                            ])
                            ->default('search')
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('embed_code')
                            ->label('كود الإيفريم')
                            ->helperText('الصق كود iframe الكامل أو رابط الصفحة المراد عرضها')
                            ->placeholder('<iframe src="https://example.com/results" ...></iframe> أو https://example.com/results')
                            ->rows(4)
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get) => $get('result_service_type') === 'embed'),

                        // إعدادات القص للـ iframe
                        Forms\Components\Fieldset::make('إعدادات قص الإيفريم')
                            ->schema([
                                Forms\Components\Toggle::make('iframe_crop_enabled')
                                    ->label('تفعيل القص')
                                    ->helperText('قص أجزاء من الإيفريم (الهيدر/الفوتر/الجوانب)')
                                    ->live()
                                    ->columnSpanFull(),
                                
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('iframe_crop_top')
                                            ->label('من الأعلى (px)')
                                            ->helperText('لإخفاء الهيدر')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(1000)
                                            ->suffix('px'),
                                        
                                        Forms\Components\TextInput::make('iframe_crop_right')
                                            ->label('من اليمين (px)')
                                            ->helperText('لإخفاء القائمة الجانبية')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(500)
                                            ->suffix('px'),
                                        
                                        Forms\Components\TextInput::make('iframe_crop_bottom')
                                            ->label('من الأسفل (px)')
                                            ->helperText('لإخفاء الفوتر')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(1000)
                                            ->suffix('px'),
                                        
                                        Forms\Components\TextInput::make('iframe_crop_left')
                                            ->label('من اليسار (px)')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(500)
                                            ->suffix('px'),
                                    ])
                                    ->visible(fn (Forms\Get $get) => $get('iframe_crop_enabled')),
                                
                                Forms\Components\TextInput::make('iframe_zoom')
                                    ->label('نسبة التكبير')
                                    ->helperText('1.0 = الحجم الأصلي، 0.5 = نصف الحجم')
                                    ->numeric()
                                    ->default(1.0)
                                    ->minValue(0.1)
                                    ->maxValue(2.0)
                                    ->step(0.1)
                                    ->visible(fn (Forms\Get $get) => $get('iframe_crop_enabled')),
                                
                                Forms\Components\Placeholder::make('crop_preview_info')
                                    ->label('')
                                    ->content('💡 نصيحة: افتح الموقع الخارجي وحدد المنطقة المراد عرضها. استخدم DevTools لقياس المسافات.')
                                    ->visible(fn (Forms\Get $get) => $get('iframe_crop_enabled'))
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn (Forms\Get $get) => $get('result_service_type') === 'embed')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('pdf_file_path')
                            ->label('ملف PDF')
                            ->helperText('ارفع ملف PDF للنتيجة (الحد الأقصى 50MB)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(51200)
                            ->directory('exam-pdfs')
                            ->disk('public')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get) => $get('result_service_type') === 'pdf'),

                        // معلومات جدول المحافظات
                        Forms\Components\Placeholder::make('governorate_table_info')
                            ->label('')
                            ->content('💡 عند اختيار جدول المحافظات، يمكنك رفع ملف النتيجة لكل محافظة من صفحة "المحافظات". زر التحميل سيظهر فقط للمحافظات المعتمدة (is_declared = true).')
                            ->visible(fn (Forms\Get $get) => $get('result_service_type') === 'governorate_table')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('SEO - إعدادات محركات البحث')
                    ->description('هذه الإعدادات تظهر في نتائج بحث جوجل ومحركات البحث الأخرى')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('عنوان الصفحة (Title)')
                            ->placeholder('نتيجة {اسم الشهادة} | نتيجتي')
                            ->maxLength(70)
                            ->helperText('يظهر في تاب المتصفح ونتائج البحث (يفضل 60-70 حرف). اترك فارغاً للعنوان التلقائي'),
                        
                        Forms\Components\Textarea::make('seo_description')
                            ->label('وصف الصفحة (Meta Description)')
                            ->placeholder('نتيجة {اسم الشهادة} برقم الجلوس والاسم...')
                            ->maxLength(160)
                            ->rows(2)
                            ->helperText('يظهر أسفل العنوان في نتائج البحث (يفضل 150-160 حرف)'),
                        
                        Forms\Components\Textarea::make('seo_keywords')
                            ->label('الكلمات المفتاحية (Keywords)')
                            ->placeholder('نتيجة الشهادة الإعدادية, نتيجتي, رقم الجلوس, نتائج الامتحانات')
                            ->rows(2)
                            ->helperText('أضف كلمات مفتاحية مفصولة بفاصلة (,)'),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name_ar')
                    ->label('الدولة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('result_service_type')
                    ->label('نوع الخدمة')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'search' => '🔍 بحث',
                        'embed' => '🌐 إيفريم',
                        'pdf' => '📄 PDF',
                        'governorate_table' => '📋 جدول',
                        default => '🔍 بحث',
                    })
                    ->color(fn (?string $state): string => match($state) {
                        'search' => 'primary',
                        'embed' => 'warning',
                        'pdf' => 'danger',
                        'governorate_table' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('passing_score')
                    ->label('حد النجاح')
                    ->suffix(' درجة')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('auto_calculate_status')
                    ->label('حساب تلقائي')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_result_approved')
                    ->label('النتيجة معتمدة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->label('الاسم بالإنجليزية')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country_id')
                    ->label('الدولة')
                    ->options(\App\Models\Country::pluck('name_ar', 'id'))
                    ->searchable()
                    ->placeholder('اختر الدولة')
                    ->default(fn () => null)
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query->whereRaw('1 = 0');
                        }
                        return $query->where('country_id', $data['value']);
                    }),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('recalculate_totals')
                    ->label('إعادة حساب المجاميع')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('إعادة حساب المجاميع')
                    ->modalDescription(function (ExamType $record) {
                        $count = \App\Models\Result::where('exam_type_id', $record->id)
                            ->whereNotNull('subjects_data')
                            ->count();
                        return "سيتم إعادة حساب المجموع الكلي لـ {$count} نتيجة في الخلفية. العملية قد تستغرق بضع دقائق.";
                    })
                    ->modalSubmitActionLabel('نعم، ابدأ الحساب')
                    ->action(function (ExamType $record) {
                        // Dispatch job to queue
                        \App\Jobs\RecalculateTotalScoresJob::dispatch($record->id, auth()->id());
                        
                        \Filament\Notifications\Notification::make()
                            ->title('بدأت عملية إعادة الحساب')
                            ->body("جاري إعادة حساب المجاميع في الخلفية. يمكنك متابعة التقدم من زر 'حالة الحساب'.")
                            ->info()
                            ->persistent()
                            ->send();
                    }),
                Tables\Actions\Action::make('check_recalculate_status')
                    ->label('حالة الحساب')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->modalHeading('حالة إعادة حساب المجاميع')
                    ->modalContent(function (ExamType $record) {
                        $cacheKey = \App\Jobs\RecalculateTotalScoresJob::progressKey($record->id);
                        $progress = \Illuminate\Support\Facades\Cache::get($cacheKey);
                        
                        if (!$progress) {
                            return new \Illuminate\Support\HtmlString('<div class="text-gray-500 text-center py-4">لا توجد عملية حساب جارية أو مكتملة</div>');
                        }
                        
                        $statusBadge = match($progress['status']) {
                            'running' => '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">⏳ جاري التنفيذ</span>',
                            'completed' => '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">✅ مكتمل</span>',
                            'failed' => '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">❌ فشل</span>',
                            default => '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">غير معروف</span>',
                        };
                        
                        $percentage = $progress['percentage'] ?? 0;
                        $progressBar = '<div class="w-full bg-gray-200 rounded-full h-4 mt-2">
                            <div class="bg-blue-600 h-4 rounded-full transition-all" style="width: ' . $percentage . '%"></div>
                        </div>';
                        
                        $html = '<div class="space-y-4 text-right" dir="rtl">';
                        $html .= '<div class="flex justify-between items-center"><span class="font-bold">الحالة:</span> ' . $statusBadge . '</div>';
                        $html .= '<div>' . $progressBar . '</div>';
                        $html .= '<div class="text-center font-bold text-lg">' . $percentage . '%</div>';
                        $html .= '<div class="grid grid-cols-2 gap-4 text-sm">';
                        $html .= '<div><span class="text-gray-500">الإجمالي:</span> <strong>' . number_format($progress['total'] ?? 0) . '</strong></div>';
                        $html .= '<div><span class="text-gray-500">تم معالجته:</span> <strong>' . number_format($progress['processed'] ?? 0) . '</strong></div>';
                        $html .= '<div><span class="text-gray-500">تم تحديثه:</span> <strong class="text-green-600">' . number_format($progress['updated'] ?? 0) . '</strong></div>';
                        $html .= '<div><span class="text-gray-500">بدأ في:</span> <strong>' . ($progress['started_at'] ?? '-') . '</strong></div>';
                        $html .= '</div>';
                        
                        if (isset($progress['completed_at'])) {
                            $html .= '<div class="text-sm"><span class="text-gray-500">انتهى في:</span> <strong>' . $progress['completed_at'] . '</strong></div>';
                        }
                        
                        if (isset($progress['error'])) {
                            $html .= '<div class="text-red-600 text-sm mt-2">خطأ: ' . $progress['error'] . '</div>';
                        }
                        
                        $html .= '</div>';
                        
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('إغلاق'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100, 200, 500, 1000])
            ->defaultPaginationPageOption(50);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamTypes::route('/'),
            'create' => Pages\CreateExamType::route('/create'),
            'edit' => Pages\EditExamType::route('/{record}/edit'),
        ];
    }
}
