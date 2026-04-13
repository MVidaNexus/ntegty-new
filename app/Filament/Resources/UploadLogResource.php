<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UploadLogResource\Pages;
use App\Models\UploadLog;
use App\Models\ExamType;
use App\Models\Governorate;
use App\Services\FileImportService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class UploadLogResource extends Resource
{
    protected static ?string $model = UploadLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $modelLabel = 'سجل رفع';
    protected static ?string $pluralModelLabel = 'سجلات الرفع';
    protected static ?string $navigationLabel = 'سجلات الرفع';
    protected static ?string $navigationGroup = 'النتائج';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // === معلومات أساسية ===
                Forms\Components\Section::make('معلومات السجل')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('upload_type')
                                    ->label('نوع السجل')
                                    ->options([
                                        UploadLog::TYPE_EXCEL => '📊 ملف Excel',
                                        UploadLog::TYPE_PDF => '📄 ملف PDF',
                                        UploadLog::TYPE_EMBED => '🌐 رابط خارجي',
                                        UploadLog::TYPE_GOVERNORATE_TABLE => '📋 جدول محافظات',
                                        UploadLog::TYPE_GOVERNORATE_FILE => '🗂️ ملف محافظة',
                                    ])
                                    ->required()
                                    ->live()
                                    ->disabled(fn ($record) => $record && $record->status !== 'pending'),
                                
                                Forms\Components\TextInput::make('batch_name')
                                    ->label('اسم السجل')
                                    ->required()
                                    ->maxLength(255),
                                
                                Forms\Components\Select::make('status')
                                    ->label('الحالة')
                                    ->options([
                                        'pending' => 'انتظار',
                                        'processing' => 'معالجة',
                                        'completed' => 'مكتمل',
                                        'failed' => 'فشل',
                                    ])
                                    ->required(),
                            ]),
                            
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('academic_year_id')
                                    ->relationship('academicYear', 'year')
                                    ->label('السنة الدراسية')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                    
                                Forms\Components\Select::make('exam_type_id')
                                    ->relationship('examType', 'name_ar')
                                    ->label('نوع الامتحان')
                                    ->live()
                                    ->afterStateUpdated(fn (Forms\Set $set) => $set('branch_id', null))
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('governorate_id')
                                    ->relationship('governorate', 'name_ar')
                                    ->label('المحافظة')
                                    ->searchable()
                                    ->preload(),
                            ]),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('branch_id')
                                    ->label('الشعبة/النظام')
                                    ->options(function (Forms\Get $get, $record) {
                                        $examTypeId = $get('exam_type_id') ?? $record?->exam_type_id;
                                        if (!$examTypeId) return [];
                                        return \App\Models\ExamBranch::where('exam_type_id', $examTypeId)
                                            ->where('is_active', true)
                                            ->orderBy('sort_order')
                                            ->pluck('name_ar', 'id');
                                    })
                                    ->visible(function (Forms\Get $get, $record) {
                                        $examTypeId = $get('exam_type_id') ?? $record?->exam_type_id;
                                        if (!$examTypeId) return false;
                                        return \App\Models\ExamBranch::where('exam_type_id', $examTypeId)->where('is_active', true)->exists();
                                    })
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('system_type')
                                    ->label('نظام الدراسة')
                                    ->options([
                                        'old' => 'نظام قديم',
                                        'new' => 'نظام حديث',
                                    ])
                                    ->visible(function (Forms\Get $get, $record) {
                                        $examTypeId = $get('exam_type_id') ?? $record?->exam_type_id;
                                        if ($examTypeId) {
                                            $examType = \App\Models\ExamType::find($examTypeId);
                                            if ($examType && str_contains($examType->code, 'secondary')) {
                                                return true;
                                            }
                                        }
                                        return false;
                                    }),
                                    
                                Forms\Components\Select::make('semester')
                                    ->label('الفصل الدراسي')
                                    ->options(\App\Models\Result::getSemesterOptions())
                                    ->default(0)
                                    ->helperText('اختر الفصل الدراسي لحساب الحالة والنسبة المئوية بشكل صحيح')
                                    ->visible(function (Forms\Get $get, $record) {
                                        $examTypeId = $get('exam_type_id') ?? $record?->exam_type_id;
                                        if ($examTypeId) {
                                            $examType = \App\Models\ExamType::find($examTypeId);
                                            // Show for preparatory and primary exams
                                            if ($examType && (str_contains($examType->code, 'preparatory') || str_contains($examType->code, 'primary'))) {
                                                return true;
                                            }
                                        }
                                        return false;
                                    }),
                            ]),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                // === قسم Excel ===
                Forms\Components\Section::make('ملف Excel')
                    ->visible(fn (Get $get, $record) => ($get('upload_type') ?? $record?->upload_type) === UploadLog::TYPE_EXCEL)
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('ملف الإكسيل')
                            ->disk('local')
                            ->directory('results_uploads')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->required(fn (Get $get) => $get('upload_type') === UploadLog::TYPE_EXCEL)
                            ->columnSpanFull()
                            ->helperText('يجب أن يحتوي الملف على صف رؤوس الأعمدة (Headers).'),
                    ]),

                // === قسم Mapping للـ Excel ===
                Forms\Components\Section::make('تعيين الأعمدة (Mapping)')
                    ->description('قم بربط أعمدة ملف الإكسيل بحقول قاعدة البيانات.')
                    ->visible(fn (Get $get, $record) => 
                        ($get('upload_type') ?? $record?->upload_type) === UploadLog::TYPE_EXCEL 
                        && $record
                    )
                    ->schema(function (Forms\Get $get, $record) {
                        if (!$record) {
                            return [];
                        }

                        $headers = $record->mapping_data['_headers'] ?? [];
                        
                        if (empty($headers) && $record->file_path && Storage::disk('local')->exists($record->file_path)) {
                            $fullPath = Storage::disk('local')->path($record->file_path);
                            $service = new FileImportService();
                            $headers = $service->getHeaders($fullPath);
                            
                            if (!empty($headers)) {
                                $mapping = $record->mapping_data ?? [];
                                $mapping['_headers'] = $headers;
                                $record->updateQuietly(['mapping_data' => $mapping]);
                            }
                        }
                        
                        if (empty($headers)) {
                            return [
                                Forms\Components\Placeholder::make('no_headers')
                                    ->label('')
                                    ->content(new HtmlString('
                                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg dark:bg-yellow-900/20 dark:border-yellow-800">
                                            <p class="text-yellow-800 dark:text-yellow-300">⚠️ لم يتم استخراج الأعمدة من الملف. تأكد من أن الملف موجود وسليم.</p>
                                        </div>
                                    '))
                                    ->columnSpanFull(),
                            ];
                        }
                        
                        $options = array_combine($headers, $headers);
                        $isCompleted = $record->status === 'completed';

                        $fields = [
                            Forms\Components\Select::make('mapping_data.seat_number')
                                ->label('عمود رقم الجلوس')
                                ->options($options)
                                ->required()
                                ->searchable(),
                                
                            Forms\Components\Select::make('mapping_data.student_name')
                                ->label('عمود اسم الطالب')
                                ->options($options)
                                ->required()
                                ->searchable(),
                            
                            Forms\Components\Select::make('mapping_data.administration')
                                ->label('عمود الإدارة (اختياري)')
                                ->options($options)
                                ->searchable()
                                ->helperText('اختر العمود الذي يحتوي على اسم الإدارة التعليمية'),
                                
                            Forms\Components\Select::make('mapping_data.school')
                                ->label('عمود المدرسة (اختياري)')
                                ->options($options)
                                ->searchable()
                                ->helperText('اختر العمود الذي يحتوي على اسم المدرسة'),
                            
                            Forms\Components\Toggle::make('mapping_data.auto_calculate_total')
                                ->label('حساب المجموع تلقائياً')
                                ->helperText('عند التفعيل، سيتم حساب المجموع من جمع درجات المواد')
                                ->default(false)
                                ->live()
                                ->columnSpanFull(),
                                
                            Forms\Components\Select::make('mapping_data.total_score')
                                ->label('عمود المجموع الكلي')
                                ->options($options)
                                ->searchable()
                                ->required(fn (Forms\Get $get) => !$get('mapping_data.auto_calculate_total'))
                                ->visible(fn (Forms\Get $get) => !$get('mapping_data.auto_calculate_total'))
                                ->helperText('اختر العمود الذي يحتوي على المجموع الكلي'),
                                
                            Forms\Components\Select::make('mapping_data.status')
                                ->label('عمود حالة الطالب (اختياري)')
                                ->options($options)
                                ->searchable()
                                ->helperText('إذا لم يتم تحديده، سيتم حساب الحالة بناءً على المجموع.'),
                                
                            Forms\Components\CheckboxList::make('mapping_data.ignored_columns')
                                ->label('أعمدة للتجاهل')
                                ->options($options)
                                ->columns(3)
                                ->helperText('حدد الأعمدة التي لا تريد استيرادها.'),
                        ];
                        
                        // إضافة تنبيه للملفات المكتملة
                        if ($isCompleted) {
                            array_unshift($fields, 
                                Forms\Components\Placeholder::make('completed_notice')
                                    ->label('')
                                    ->content(new HtmlString('
                                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
                                            <p class="text-blue-800 dark:text-blue-300 font-semibold">ℹ️ هذا الملف تمت معالجته مسبقاً</p>
                                            <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">يمكنك تعديل تعيين الأعمدة وإعادة الاستيراد من الأزرار بالأعلى.</p>
                                        </div>
                                    '))
                                    ->columnSpanFull()
                            );
                        }
                        
                        return $fields;
                    }),

                // === قسم PDF ===
                Forms\Components\Section::make('ملف PDF')
                    ->visible(fn (Get $get, $record) => 
                        in_array($get('upload_type') ?? $record?->upload_type, [UploadLog::TYPE_PDF, UploadLog::TYPE_GOVERNORATE_FILE])
                    )
                    ->schema([
                        Forms\Components\FileUpload::make('extra_data.pdf_file')
                            ->label('ملف PDF')
                            ->disk('public')
                            ->directory('exam-pdfs')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(51200)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpanFull()
                            ->helperText('الحد الأقصى 50 ميجابايت'),
                        
                        Forms\Components\Toggle::make('apply_pdf_to_exam')
                            ->label('تطبيق على الشهادة')
                            ->helperText('عند الحفظ، سيتم تحديث ملف PDF في نوع الشهادة')
                            ->default(true)
                            ->visible(fn (Get $get, $record) => ($get('upload_type') ?? $record?->upload_type) === UploadLog::TYPE_PDF),
                    ]),

                // === قسم Embed/iFrame ===
                Forms\Components\Section::make('إعدادات الـ iFrame')
                    ->visible(fn (Get $get, $record) => ($get('upload_type') ?? $record?->upload_type) === UploadLog::TYPE_EMBED)
                    ->schema([
                        Forms\Components\Radio::make('extra_data.input_type')
                            ->label('نوع الإدخال')
                            ->options([
                                'url' => '🔗 رابط مباشر (URL)',
                                'code' => '📝 كود iFrame كامل',
                            ])
                            ->default('url')
                            ->live()
                            ->inline()
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('extra_data.embed_url')
                            ->label('رابط الصفحة')
                            ->url()
                            ->placeholder('https://example.com/results')
                            ->visible(fn (Get $get) => ($get('extra_data.input_type') ?? 'url') === 'url')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('extra_data.embed_code')
                            ->label('كود iFrame')
                            ->placeholder('<iframe src="https://..." width="100%" height="600"></iframe>')
                            ->visible(fn (Get $get) => ($get('extra_data.input_type') ?? 'url') === 'code')
                            ->rows(4)
                            ->columnSpanFull(),
                        
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('extra_data.width')
                                    ->label('العرض')
                                    ->placeholder('100%')
                                    ->default('100%'),
                                
                                Forms\Components\TextInput::make('extra_data.height')
                                    ->label('الارتفاع')
                                    ->placeholder('600px')
                                    ->default('600px'),
                                
                                Forms\Components\Select::make('extra_data.position')
                                    ->label('المحاذاة')
                                    ->options([
                                        'center' => 'وسط',
                                        'left' => 'يسار',
                                        'right' => 'يمين',
                                    ])
                                    ->default('center'),
                            ]),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('extra_data.scrolling')
                                    ->label('السماح بالتمرير')
                                    ->default(true),
                                
                                Forms\Components\Toggle::make('extra_data.border')
                                    ->label('إظهار الإطار')
                                    ->default(false),
                            ]),
                        
                        Forms\Components\Section::make('تحديد منطقة (قص)')
                            ->description('اسحب بالماوس لتحديد المنطقة المراد عرضها من الأربع جهات')
                            ->collapsed()
                            ->schema([
                                Forms\Components\Toggle::make('extra_data.crop_enabled')
                                    ->label('تفعيل تحديد المنطقة')
                                    ->default(false)
                                    ->live()
                                    ->columnSpanFull(),
                                
                                // أداة القص التفاعلية بالماوس
                                Forms\Components\ViewField::make('crop_tool_edit')
                                    ->view('filament.components.iframe-crop-tool-advanced')
                                    ->visible(fn (Get $get) => $get('extra_data.crop_enabled') || $get('extra_data.embed_url') || $get('extra_data.embed_code'))
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                                
                                Forms\Components\Grid::make(4)
                                    ->visible(fn (Get $get) => $get('extra_data.crop_enabled'))
                                    ->schema([
                                        Forms\Components\TextInput::make('extra_data.crop_top')
                                            ->label('من الأعلى (px)')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->default(0),
                                        
                                        Forms\Components\TextInput::make('extra_data.crop_right')
                                            ->label('من اليمين (px)')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->default(0),
                                        
                                        Forms\Components\TextInput::make('extra_data.crop_bottom')
                                            ->label('من الأسفل (px)')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->default(0),
                                        
                                        Forms\Components\TextInput::make('extra_data.crop_left')
                                            ->label('من اليسار (px)')
                                            ->numeric()
                                            ->live(onBlur: true)
                                            ->default(0),
                                    ]),
                                    
                                Forms\Components\TextInput::make('extra_data.zoom')
                                    ->label('نسبة التكبير (%)')
                                    ->numeric()
                                    ->live(onBlur: true)
                                    ->default(100)
                                    ->visible(fn (Get $get) => $get('extra_data.crop_enabled'))
                                    ->columnSpanFull(),
                            ]),
                        
                        Forms\Components\Placeholder::make('auto_apply_notice')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                    <p class="text-sm text-green-700 dark:text-green-300">
                                        <span class="font-bold">✅ تطبيق تلقائي:</span>
                                        عند الحفظ، سيتم تحديث إعدادات الـ iFrame تلقائياً على المحافظة المختارة
                                    </p>
                                </div>
                            '))
                            ->columnSpanFull(),
                    ]),

                // === قسم جدول المحافظات ===
                Forms\Components\Section::make('إعدادات جدول المحافظات')
                    ->visible(fn (Get $get, $record) => ($get('upload_type') ?? $record?->upload_type) === UploadLog::TYPE_GOVERNORATE_TABLE)
                    ->schema([
                        Forms\Components\Placeholder::make('gov_info')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record) return '';
                                $extraData = $record->extra_data ?? [];
                                $governorates = $extraData['governorates'] ?? [];
                                $filesUploaded = $extraData['files_uploaded'] ?? 0;
                                
                                return new HtmlString('
                                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                        <p class="text-amber-800 dark:text-amber-300">
                                            📋 عدد المحافظات: <strong>' . count($governorates) . '</strong>
                                            | ملفات مرفوعة: <strong>' . $filesUploaded . '</strong>
                                        </p>
                                    </div>
                                ');
                            })
                            ->columnSpanFull(),
                        
                        Forms\Components\Toggle::make('apply_gov_table_to_exam')
                            ->label('تطبيق على الشهادة')
                            ->helperText('عند الحفظ، سيتم تفعيل/إلغاء وضع جدول المحافظات في نوع الشهادة')
                            ->default(true),
                    ]),

                // === قسم ملف المحافظة ===
                Forms\Components\Section::make('ملف المحافظة')
                    ->visible(fn (Get $get, $record) => ($get('upload_type') ?? $record?->upload_type) === UploadLog::TYPE_GOVERNORATE_FILE)
                    ->schema([
                        Forms\Components\Toggle::make('extra_data.is_declared')
                            ->label('النتيجة معتمدة')
                            ->helperText('إظهار زر التحميل للطلاب'),
                        
                        Forms\Components\Toggle::make('apply_gov_file')
                            ->label('تطبيق على المحافظة')
                            ->helperText('عند الحفظ، سيتم تحديث ملف المحافظة وحالة الاعتماد')
                            ->default(true),
                    ]),

                // === إحصائيات المعالجة ===
                Forms\Components\Section::make('إحصائيات المعالجة')
                    ->visible(fn ($record) => $record && $record->upload_type === UploadLog::TYPE_EXCEL)
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('records_count')
                                    ->label('إجمالي السجلات')
                                    ->disabled(),
                                Forms\Components\TextInput::make('processed_rows')
                                    ->label('تمت معالجتها')
                                    ->disabled(),
                                Forms\Components\TextInput::make('successful_rows')
                                    ->label('ناجحة')
                                    ->disabled(),
                                Forms\Components\TextInput::make('failed_rows')
                                    ->label('فاشلة')
                                    ->disabled(),
                            ]),
                        Forms\Components\Textarea::make('error_message')
                            ->label('رسائل الخطأ')
                            ->columnSpanFull()
                            ->disabled()
                            ->visible(fn ($state) => !empty($state)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('upload_type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->upload_type_icon . ' ' . $record->upload_type_label)
                    ->color(fn ($record) => $record->upload_type_color)
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('batch_name')
                    ->label('الاسم/الوصف')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('medium')
                    ->description(function ($record) {
                        $parts = [];
                        if ($record->examType) {
                            $parts[] = $record->examType->name_ar;
                        }
                        if ($record->governorate) {
                            $parts[] = $record->governorate->name_ar;
                        }
                        if ($record->branch) {
                            $parts[] = $record->branch->name_ar;
                        }
                        if ($record->system_type) {
                            $parts[] = $record->system_type === 'old' ? 'قديم' : 'حديث';
                        }
                        return implode(' - ', $parts);
                    }),
                
                Tables\Columns\TextColumn::make('examType.name_ar')
                    ->label('الشهادة')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('governorate.name_ar')
                    ->label('المحافظة')
                    ->placeholder('موحد')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'انتظار',
                        'processing' => 'معالجة',
                        'completed' => 'مكتمل',
                        'failed' => 'فشل',
                        default => $state,
                    }),
                
                Tables\Columns\ViewColumn::make('progress')
                    ->label('التقدم')
                    ->view('filament.tables.columns.progress-bar')
                    ->alignCenter()
                    ->visible(fn ($record) => $record && $record->upload_type === UploadLog::TYPE_EXCEL),
                
                Tables\Columns\TextColumn::make('records_count')
                    ->label('السجلات')
                    ->numeric()
                    ->sortable()
                    ->visible(fn ($record) => $record && in_array($record->upload_type, [UploadLog::TYPE_EXCEL, UploadLog::TYPE_GOVERNORATE_TABLE])),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->date('d/m/Y')
                    ->sortable()
                    ->description(fn ($record) => $record->created_at->format('H:i'))
                    ->size('sm'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('upload_type')
                    ->label('نوع السجل')
                    ->options([
                        UploadLog::TYPE_EXCEL => '📊 ملف Excel',
                        UploadLog::TYPE_PDF => '📄 ملف PDF',
                        UploadLog::TYPE_EMBED => '🌐 رابط خارجي',
                        UploadLog::TYPE_GOVERNORATE_TABLE => '📋 جدول محافظات',
                        UploadLog::TYPE_GOVERNORATE_FILE => '🗂️ ملف محافظة',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'انتظار',
                        'processing' => 'معالجة',
                        'completed' => 'مكتمل',
                        'failed' => 'فشل',
                    ]),
                Tables\Filters\SelectFilter::make('exam_type_id')
                    ->label('الشهادة')
                    ->relationship('examType', 'name_ar'),
            ])
            ->actions([
                Tables\Actions\Action::make('apply_changes')
                    ->label('تطبيق')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تطبيق التغييرات')
                    ->modalDescription('سيتم تطبيق إعدادات هذا السجل على الشهادة/المحافظة. هل تريد المتابعة؟')
                    ->visible(fn ($record) => $record && in_array($record->upload_type, [
                        UploadLog::TYPE_PDF, 
                        UploadLog::TYPE_EMBED, 
                        UploadLog::TYPE_GOVERNORATE_TABLE,
                        UploadLog::TYPE_GOVERNORATE_FILE
                    ]))
                    ->action(function (UploadLog $record) {
                        self::applyRecordToDatabase($record);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم التطبيق بنجاح')
                            ->body('تم تطبيق الإعدادات على قاعدة البيانات')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\Action::make('recalculate_totals')
                    ->label('إعادة حساب')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('إعادة حساب المجاميع')
                    ->modalDescription('سيتم إعادة حساب المجموع الكلي لجميع نتائج هذا الملف.')
                    ->visible(fn ($record) => $record && $record->upload_type === UploadLog::TYPE_EXCEL && $record->status === 'completed')
                    ->action(function (UploadLog $record) {
                        $examType = $record->examType;
                        if (!$examType) {
                            \Filament\Notifications\Notification::make()
                                ->title('خطأ')
                                ->body('لم يتم العثور على نوع الشهادة')
                                ->danger()
                                ->send();
                            return;
                        }
                        
                        $updated = 0;
                        
                        \App\Models\Result::where('upload_log_id', $record->id)
                            ->whereNotNull('subjects_data')
                            ->chunkById(500, function ($results) use ($examType, &$updated) {
                                foreach ($results as $result) {
                                    if (!empty($result->subjects_data)) {
                                        $newTotal = $examType->calculateTotalScore($result->subjects_data);
                                        if ($newTotal > 0) {
                                            $result->total_score = $newTotal;
                                            $result->save();
                                            $updated++;
                                        }
                                    }
                                }
                            });
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم إعادة الحساب')
                            ->body("تم تحديث مجاميع {$updated} نتيجة")
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-m-pencil-square')
                    ->button(),
                
                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100, 200, 500, 1000])
            ->defaultPaginationPageOption(50);
    }

    /**
     * تطبيق إعدادات السجل على قاعدة البيانات
     */
    public static function applyRecordToDatabase(UploadLog $record): void
    {
        $extraData = $record->extra_data ?? [];
        
        if ($record->upload_type === UploadLog::TYPE_PDF) {
            $examType = ExamType::find($record->exam_type_id);
            if ($examType && isset($extraData['pdf_file'])) {
                $examType->update([
                    'result_service_type' => 'pdf',
                    'pdf_file_path' => $extraData['pdf_file'],
                ]);
            }
        }
        
        elseif ($record->upload_type === UploadLog::TYPE_EMBED) {
            $examType = ExamType::find($record->exam_type_id);
            if ($examType) {
                $inputType = $extraData['input_type'] ?? 'url';
                $embedCode = $inputType === 'url' 
                    ? ($extraData['embed_url'] ?? '') 
                    : ($extraData['embed_code'] ?? '');
                
                $examType->update([
                    'result_service_type' => 'embed',
                    'embed_code' => $embedCode,
                    'iframe_width' => $extraData['width'] ?? '100%',
                    'iframe_height' => $extraData['height'] ?? '600px',
                    'iframe_position' => $extraData['position'] ?? 'center',
                    'iframe_scrolling' => $extraData['scrolling'] ?? true,
                    'iframe_border' => $extraData['border'] ?? false,
                    'iframe_crop_enabled' => $extraData['crop_enabled'] ?? false,
                    'iframe_crop_top' => $extraData['crop_top'] ?? '0',
                    'iframe_crop_left' => $extraData['crop_left'] ?? '0',
                    'iframe_zoom' => $extraData['zoom'] ?? '100',
                ]);
            }
        }
        
        elseif ($record->upload_type === UploadLog::TYPE_GOVERNORATE_TABLE) {
            $examType = ExamType::find($record->exam_type_id);
            if ($examType) {
                $examType->update(['result_service_type' => 'governorate_table']);
            }
        }
        
        elseif ($record->upload_type === UploadLog::TYPE_GOVERNORATE_FILE) {
            $governorate = Governorate::find($record->governorate_id);
            if ($governorate) {
                $updateData = [];
                if (isset($extraData['pdf_file'])) {
                    $updateData['result_pdf_path'] = $extraData['pdf_file'];
                }
                if (isset($extraData['is_declared'])) {
                    $updateData['is_declared'] = $extraData['is_declared'];
                }
                if (!empty($updateData)) {
                    $governorate->update($updateData);
                }
            }
        }
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
            'index' => Pages\ListUploadLogs::route('/'),
            'edit' => Pages\EditUploadLog::route('/{record}/edit'),
        ];
    }
}