<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GovernorateResource\Pages;
use App\Filament\Resources\GovernorateResource\RelationManagers;
use App\Models\Governorate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GovernorateResource extends Resource
{
    protected static ?string $model = Governorate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'محافظة';
    protected static ?string $pluralModelLabel = 'المحافظات';
    protected static ?string $navigationLabel = 'المحافظات';
    protected static ?string $navigationGroup = 'إدارة البيانات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('البيانات الأساسية')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('البيانات الأساسية')
                                    ->schema([
                                        Forms\Components\Select::make('country_id')
                                            ->label('الدولة')
                                            ->relationship('country', 'name_ar')
                                            ->required(),
                                        Forms\Components\TextInput::make('name_ar')
                                            ->label('اسم المحافظة (عربي)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('name_en')
                                            ->label('اسم المحافظة (إنجليزي)')
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, ?string $old) {
                                                if (($get('slug') ?? '') !== \Illuminate\Support\Str::slug($old ?? '')) {
                                                    return;
                                                }
                                                
                                                $slugSource = $state ?? $get('name_ar');
                                                $set('slug', \Illuminate\Support\Str::slug($slugSource));
                                            }),
                                            
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        Forms\Components\Toggle::make('is_declared')
                                            ->label('تم اعتماد النتيجة')
                                            ->helperText('عند تفعيل هذا الخيار، سيظهر زر التحميل للمحافظة في جدول المحافظات')
                                            ->default(false)
                                            ->live(),
                                    ])->columns(2),

                                Forms\Components\Section::make('شعار المحافظة')
                                    ->schema([
                                        Forms\Components\FileUpload::make('logo_path')
                                            ->label('شعار المحافظة')
                                            ->image()
                                            ->directory('governorate-logos')
                                            ->disk('public')
                                            ->imagePreviewHeight('150')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                                            ->helperText('الحد الأقصى 2MB - صيغ مدعومة: JPG, PNG, GIF, WebP, SVG')
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('ملف نتيجة المحافظة')
                                    ->description('ملف PDF للنتيجة - يُستخدم عند اختيار "جدول المحافظات" كنوع خدمة في الشهادة')
                                    ->schema([
                                        Forms\Components\FileUpload::make('result_pdf_path')
                                            ->label('ملف النتيجة PDF')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->directory('governorate-results')
                                            ->disk('public')
                                            ->maxSize(51200) // 50MB
                                            ->downloadable()
                                            ->openable()
                                            ->helperText('ارفع ملف PDF للنتيجة (الحد الأقصى 50MB). زر التحميل سيكون متاحاً فقط عند تفعيل "تم اعتماد النتيجة"')
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\Placeholder::make('result_status_hint')
                                            ->label('')
                                            ->content(fn (Get $get) => $get('is_declared') 
                                                ? '✅ النتيجة معتمدة - زر التحميل سيكون متاحاً للطلاب'
                                                : '⏳ النتيجة غير معتمدة - سيظهر "قريباً" للطلاب')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\Section::make('إعدادات SEO للمحافظة')
                                    ->description('هذه الإعدادات تُستخدم لتحسين ظهور صفحة المحافظة في محركات البحث')
                                    ->schema([
                                        Forms\Components\TextInput::make('seo_title')
                                            ->label('عنوان SEO')
                                            ->helperText('عنوان الصفحة في محركات البحث - اتركه فارغاً للاستخدام التلقائي')
                                            ->placeholder('نتيجة الشهادة الإعدادية محافظة الشرقية')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('seo_description')
                                            ->label('وصف SEO')
                                            ->helperText('وصف الصفحة في نتائج البحث (150-160 حرف)')
                                            ->placeholder('نتيجة الشهادة الإعدادية محافظة الشرقية - ابحث عن نتيجتك برقم الجلوس أو الاسم')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('seo_keywords')
                                            ->label('الكلمات المفتاحية')
                                            ->helperText('أضف كلمات مفتاحية مفصولة بفاصلة (,) - مثال: نتيجة الشهادة الإعدادية, محافظة القاهرة, نتيجتي')
                                            ->placeholder('نتيجة الشهادة الإعدادية بالقاهرة, محافظة القاهرة, نتيجة الصف الثالث الإعدادي 2025')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('المحتوى')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('صندوق المحتوى')
                                    ->description('محتوى إضافي يظهر في صفحة المحافظة لتحسين SEO وتجربة المستخدم')
                                    ->schema([
                                        Forms\Components\Toggle::make('show_content_section')
                                            ->label('إظهار قسم المحتوى')
                                            ->helperText('عند التفعيل سيظهر صندوق المحتوى في صفحة المحافظة')
                                            ->default(true)
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('content_title')
                                            ->label('عنوان المحتوى')
                                            ->placeholder('نتيجة الشهادة الإعدادية محافظة الشرقية')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('content_intro')
                                            ->label('مقدمة المحتوى')
                                            ->placeholder('مقدمة قصيرة عن نتيجة المحافظة...')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('content_body')
                                            ->label('المحتوى الرئيسي')
                                            ->placeholder('اكتب محتوى تفصيلي عن نتيجة المحافظة...')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                                'blockquote',
                                                'h2',
                                                'h3',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),
                                        Forms\Components\Placeholder::make('content_hint')
                                            ->label('')
                                            ->content('💡 نصيحة: اذكر اسم المحافظة والشهادة والدولة في المحتوى لتحسين SEO')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('خدمة النتائج')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('حالة عرض النتيجة')
                                    ->description('يتم التحكم في هذه الإعدادات من خلال "سجلات الرفع" في قائمة النتائج')
                                    ->schema([
                                        Forms\Components\Placeholder::make('service_info')
                                            ->label('')
                                            ->content(function ($record) {
                                                if (!$record) return 'لم يتم تحديد نوع الخدمة بعد';
                                                
                                                $type = $record->result_service_type ?? 'search';
                                                $typeLabels = [
                                                    'search' => '🔍 بحث عادي (قاعدة البيانات)',
                                                    'embed' => '🌐 موقع خارجي (iFrame)',
                                                    'pdf' => '📄 ملف PDF',
                                                ];
                                                
                                                $html = '<div class="space-y-3">';
                                                $html .= '<div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">';
                                                $html .= '<span class="font-bold">نوع الخدمة الحالي:</span> ' . ($typeLabels[$type] ?? $type);
                                                $html .= '</div>';
                                                
                                                if ($type === 'embed' && $record->embed_code) {
                                                    $html .= '<div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">';
                                                    $html .= '<span class="font-bold">رابط iFrame:</span><br>';
                                                    $html .= '<code class="text-xs break-all">' . e($record->embed_code) . '</code>';
                                                    $html .= '</div>';
                                                    
                                                    if ($record->iframe_crop_enabled) {
                                                        $html .= '<div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">';
                                                        $html .= '<span class="font-bold">القص مفعّل:</span> ';
                                                        $html .= 'أعلى: ' . ($record->iframe_crop_top ?? 0) . 'px | ';
                                                        $html .= 'يمين: ' . ($record->iframe_crop_right ?? 0) . 'px | ';
                                                        $html .= 'أسفل: ' . ($record->iframe_crop_bottom ?? 0) . 'px | ';
                                                        $html .= 'يسار: ' . ($record->iframe_crop_left ?? 0) . 'px';
                                                        $html .= '</div>';
                                                    }
                                                }
                                                
                                                $html .= '<div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg text-sm">';
                                                $html .= '💡 لتعديل هذه الإعدادات، اذهب إلى <strong>النتائج ← سجلات الرفع</strong> وأنشئ سجل جديد من نوع "رابط خارجي"';
                                                $html .= '</div>';
                                                $html .= '</div>';
                                                
                                                return new \Illuminate\Support\HtmlString($html);
                                            })
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('الشعار')
                    ->disk('public')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name_ar ?? 'G') . '&color=7F9CF5&background=EBF4FF'),
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country.name_ar')
                    ->label('الدولة')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_declared')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('result_service_type')
                    ->label('نوع الخدمة')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'embed' => '🌐 iFrame',
                        'pdf' => '📄 PDF',
                        default => '🔍 بحث',
                    })
                    ->color(fn ($state) => match ($state) {
                        'embed' => 'info',
                        'pdf' => 'danger',
                        default => 'success',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('result_pdf_path')
                    ->label('ملف PDF')
                    ->icon(fn ($state) => $state ? 'heroicon-o-document-check' : 'heroicon-o-document')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state ? 'تم رفع ملف النتيجة' : 'لم يتم رفع ملف'),
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
                Tables\Filters\TernaryFilter::make('is_declared')
                    ->label('حالة النتيجة')
                    ->trueLabel('معتمدة')
                    ->falseLabel('غير معتمدة')
                    ->placeholder('الكل'),
                Tables\Filters\TernaryFilter::make('has_pdf')
                    ->label('ملف PDF')
                    ->trueLabel('لديها ملف')
                    ->falseLabel('بدون ملف')
                    ->placeholder('الكل')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('result_pdf_path')->where('result_pdf_path', '!=', ''),
                        false: fn (Builder $query) => $query->where(fn ($q) => $q->whereNull('result_pdf_path')->orWhere('result_pdf_path', '')),
                    ),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('upload_pdf')
                    ->label('رفع/تغيير PDF')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->form([
                        Forms\Components\FileUpload::make('result_pdf_path')
                            ->label('ملف النتيجة PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('governorate-results')
                            ->disk('public')
                            ->maxSize(51200)
                            ->required(),
                    ])
                    ->action(function (Governorate $record, array $data) {
                        $record->update(['result_pdf_path' => $data['result_pdf_path']]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم رفع الملف بنجاح')
                            ->body("تم رفع ملف النتيجة لمحافظة {$record->name_ar}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('toggle_declared')
                    ->label(fn (Governorate $record) => $record->is_declared ? 'إلغاء الاعتماد' : 'اعتماد')
                    ->icon(fn (Governorate $record) => $record->is_declared ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Governorate $record) => $record->is_declared ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Governorate $record) {
                        $record->update(['is_declared' => !$record->is_declared]);
                        
                        $status = $record->is_declared ? 'معتمدة' : 'غير معتمدة';
                        \Filament\Notifications\Notification::make()
                            ->title('تم تحديث الحالة')
                            ->body("نتيجة محافظة {$record->name_ar} أصبحت {$status}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('declare_all')
                        ->label('اعتماد المحدد')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_declared' => true])),
                    Tables\Actions\BulkAction::make('undeclare_all')
                        ->label('إلغاء اعتماد المحدد')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_declared' => false])),
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
            'index' => Pages\ListGovernorates::route('/'),
            'create' => Pages\CreateGovernorate::route('/create'),
            'edit' => Pages\EditGovernorate::route('/{record}/edit'),
        ];
    }
}
