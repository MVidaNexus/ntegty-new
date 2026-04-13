<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateSettingResource\Pages;
use App\Models\CertificateSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CertificateSettingResource extends Resource
{
    protected static ?string $model = CertificateSetting::class;

    protected static ?string $modelLabel = 'إعدادات الشهادة';
    protected static ?string $pluralModelLabel = 'إعدادات الشهادات';
    protected static ?string $navigationLabel = 'شهادة التقدير';
    protected static ?string $navigationGroup = 'إعدادات الموقع';
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        $fontOptions = CertificateSetting::getFontOptions();
        $variablesHelper = self::getVariablesHelperText();

        return $form
            ->schema([
                Forms\Components\Tabs::make('إعدادات الشهادة')
                    ->tabs([
                        // ================== تبويب الإعدادات الأساسية ==================
                        Forms\Components\Tabs\Tab::make('الإعدادات الأساسية')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('معلومات القالب')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('اسم القالب')
                                            ->required()
                                            ->maxLength(255)
                                            ->default('default'),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('نشط')
                                            ->helperText('قالب واحد فقط يمكن أن يكون نشطاً')
                                            ->default(true),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('إعدادات الصفحة')
                                    ->schema([
                                        Forms\Components\TextInput::make('page_title')
                                            ->label('عنوان الصفحة')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('page_description')
                                            ->label('وصف الصفحة')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('المتغيرات المتاحة')
                                    ->description('يمكنك استخدام هذه المتغيرات في أي سطر نصي')
                                    ->schema([
                                        Forms\Components\Placeholder::make('variables_info')
                                            ->label('')
                                            ->content(fn () => new \Illuminate\Support\HtmlString(
                                                '<div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">' .
                                                collect(CertificateSetting::getAvailableVariables())
                                                    ->map(fn ($label, $var) => "<div class='bg-gray-100 dark:bg-gray-800 rounded px-2 py-1'><code class='text-primary-600 font-mono'>{$var}</code> = {$label}</div>")
                                                    ->implode('') .
                                                '</div>'
                                            ))
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ================== تبويب التصميم ==================
                        Forms\Components\Tabs\Tab::make('التصميم')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make('صورة الخلفية')
                                    ->schema([
                                        Forms\Components\FileUpload::make('background_image')
                                            ->label('صورة خلفية الشهادة')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('certificates')
                                            ->disk('public')
                                            ->columnSpanFull()
                                            ->helperText('يفضل استخدام صورة بأبعاد 2480×1754 بيكسل (A4 أفقي)'),
                                    ]),

                                Forms\Components\Section::make('تنسيق الاسم الرئيسي')
                                    ->description('إعدادات اسم الطالب الكبير في أعلى الشهادة')
                                    ->schema([
                                        Forms\Components\Select::make('name_font_family')
                                            ->label('نوع الخط')
                                            ->options($fontOptions)
                                            ->default('Cairo')
                                            ->native(false)
                                            ->live(),
                                        Forms\Components\TextInput::make('name_font_size')
                                            ->label('حجم الخط')
                                            ->numeric()
                                            ->default(80)
                                            ->suffix('px')
                                            ->minValue(20)
                                            ->maxValue(200)
                                            ->live(),
                                        Forms\Components\ColorPicker::make('primary_color')
                                            ->label('لون الاسم')
                                            ->default('#1e3a8a')
                                            ->live(),
                                    ])
                                    ->columns(3),
                            ]),

                        // ================== تبويب النصوص - الذكور ==================
                        Forms\Components\Tabs\Tab::make('نصوص الذكور')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Placeholder::make('male_info')
                                    ->label('')
                                    ->content('النصوص التي تظهر في الشهادة للذكور. استخدم المتغيرات: ' . $variablesHelper)
                                    ->columnSpanFull(),
                                
                                ...self::createTextFields('male', 1, 'السطر الأول', 'تتقدم إدارة المدرسة والهيئة التعليمية بخالص التهاني والتبريكات'),
                                ...self::createTextFields('male', 2, 'السطر الثاني', 'للطالب المتفوق {student_name}'),
                                ...self::createTextFields('male', 3, 'السطر الثالث', 'وذلك لحصوله على مجموع {total_score} من {max_score}'),
                                ...self::createTextFields('male', 4, 'السطر الرابع', 'في {exam_type}'),
                                ...self::createTextFields('male', 5, 'السطر الخامس', 'متمنين له دوام التوفيق والنجاح'),
                                ...self::createTextFields('male', 6, 'السطر السادس', ''),
                            ]),

                        // ================== تبويب النصوص - الإناث ==================
                        Forms\Components\Tabs\Tab::make('نصوص الإناث')
                            ->icon('heroicon-o-user')
                            ->badge('♀')
                            ->schema([
                                Forms\Components\Placeholder::make('female_info')
                                    ->label('')
                                    ->content('النصوص التي تظهر في الشهادة للإناث. استخدم المتغيرات: ' . $variablesHelper)
                                    ->columnSpanFull(),
                                
                                ...self::createTextFields('female', 1, 'السطر الأول', 'تتقدم إدارة المدرسة والهيئة التعليمية بخالص التهاني والتبريكات'),
                                ...self::createTextFields('female', 2, 'السطر الثاني', 'للطالبة المتفوقة {student_name}'),
                                ...self::createTextFields('female', 3, 'السطر الثالث', 'وذلك لحصولها على مجموع {total_score} من {max_score}'),
                                ...self::createTextFields('female', 4, 'السطر الرابع', 'في {exam_type}'),
                                ...self::createTextFields('female', 5, 'السطر الخامس', 'متمنين لها دوام التوفيق والنجاح'),
                                ...self::createTextFields('female', 6, 'السطر السادس', ''),
                            ]),

                        // ================== تبويب التوقيعات ==================
                        Forms\Components\Tabs\Tab::make('التوقيعات')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Forms\Components\Section::make('التوقيع الأيسر')
                                    ->schema([
                                        Forms\Components\TextInput::make('signature_left_text')
                                            ->label('النص')
                                            ->default('مدير المدرسة')
                                            ->columnSpanFull(),
                                        Forms\Components\Select::make('signature_left_font_family')
                                            ->label('نوع الخط')
                                            ->options($fontOptions)
                                            ->default('Cairo'),
                                        Forms\Components\TextInput::make('signature_left_font_size')
                                            ->label('حجم الخط')
                                            ->numeric()
                                            ->default(45)
                                            ->suffix('px'),
                                        Forms\Components\ColorPicker::make('signature_left_color')
                                            ->label('اللون')
                                            ->default('#1e3a8a'),
                                        Forms\Components\TextInput::make('signature_left_position_x')
                                            ->label('الموضع الأفقي X')
                                            ->numeric()
                                            ->default(620)
                                            ->suffix('px'),
                                        Forms\Components\TextInput::make('signature_left_position_y')
                                            ->label('الموضع الرأسي Y')
                                            ->numeric()
                                            ->default(1500)
                                            ->suffix('px'),
                                    ])
                                    ->columns(5),

                                Forms\Components\Section::make('التوقيع الأيمن')
                                    ->schema([
                                        Forms\Components\TextInput::make('signature_right_text')
                                            ->label('النص')
                                            ->default('الكادر الإداري')
                                            ->columnSpanFull(),
                                        Forms\Components\Select::make('signature_right_font_family')
                                            ->label('نوع الخط')
                                            ->options($fontOptions)
                                            ->default('Cairo'),
                                        Forms\Components\TextInput::make('signature_right_font_size')
                                            ->label('حجم الخط')
                                            ->numeric()
                                            ->default(45)
                                            ->suffix('px'),
                                        Forms\Components\ColorPicker::make('signature_right_color')
                                            ->label('اللون')
                                            ->default('#1e3a8a'),
                                        Forms\Components\TextInput::make('signature_right_position_x')
                                            ->label('الموضع الأفقي X')
                                            ->numeric()
                                            ->default(1860)
                                            ->suffix('px'),
                                        Forms\Components\TextInput::make('signature_right_position_y')
                                            ->label('الموضع الرأسي Y')
                                            ->numeric()
                                            ->default(1500)
                                            ->suffix('px'),
                                    ])
                                    ->columns(5),

                                Forms\Components\Section::make('إعدادات إضافية')
                                    ->schema([
                                        Forms\Components\Toggle::make('show_date')
                                            ->label('إظهار التاريخ')
                                            ->default(true),
                                    ]),
                            ]),

                        // ================== تبويب مواضع وتنسيق الذكور ==================
                        Forms\Components\Tabs\Tab::make('مواضع الذكور')
                            ->icon('heroicon-o-arrows-pointing-out')
                            ->schema([
                                Forms\Components\Section::make('موضع الاسم (ذكر)')
                                    ->schema([
                                        Forms\Components\TextInput::make('name_position_x')
                                            ->label('الموضع الأفقي X')
                                            ->numeric()
                                            ->default(1240)
                                            ->suffix('px')
                                            ->minValue(0)
                                            ->maxValue(2480)
                                            ->live()
                                            ->helperText('1240 = المنتصف'),
                                        Forms\Components\TextInput::make('name_position_y')
                                            ->label('الموضع الرأسي Y')
                                            ->numeric()
                                            ->default(700)
                                            ->suffix('px')
                                            ->minValue(0)
                                            ->maxValue(1754)
                                            ->live(),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('تنسيق ومواضع الأسطر (ذكر)')
                                    ->description('تحكم في الموضع والخط واللون لكل سطر نصي للذكور')
                                    ->schema([
                                        ...self::createLinePositionFields('', 1, 'السطر الأول', 900),
                                        ...self::createLinePositionFields('', 2, 'السطر الثاني', 1000),
                                        ...self::createLinePositionFields('', 3, 'السطر الثالث', 1100),
                                        ...self::createLinePositionFields('', 4, 'السطر الرابع', 1200),
                                        ...self::createLinePositionFields('', 5, 'السطر الخامس', 1300),
                                        ...self::createLinePositionFields('', 6, 'السطر السادس', 1400),
                                    ]),

                                Forms\Components\Section::make('معاينة الشهادة (ذكر)')
                                    ->description('معاينة حية لمواضع العناصر للذكور')
                                    ->schema([
                                        Forms\Components\ViewField::make('certificate_preview_male')
                                            ->view('filament.forms.components.certificate-preview')
                                            ->viewData(['gender' => 'male'])
                                            ->dehydrated(false)
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        // ================== تبويب مواضع وتنسيق الإناث ==================
                        Forms\Components\Tabs\Tab::make('مواضع الإناث')
                            ->icon('heroicon-o-arrows-pointing-out')
                            ->badge('♀')
                            ->schema([
                                Forms\Components\Section::make('موضع الاسم (أنثى)')
                                    ->schema([
                                        Forms\Components\TextInput::make('name_position_x_female')
                                            ->label('الموضع الأفقي X')
                                            ->numeric()
                                            ->default(1240)
                                            ->suffix('px')
                                            ->minValue(0)
                                            ->maxValue(2480)
                                            ->live()
                                            ->helperText('1240 = المنتصف'),
                                        Forms\Components\TextInput::make('name_position_y_female')
                                            ->label('الموضع الرأسي Y')
                                            ->numeric()
                                            ->default(700)
                                            ->suffix('px')
                                            ->minValue(0)
                                            ->maxValue(1754)
                                            ->live(),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('تنسيق ومواضع الأسطر (أنثى)')
                                    ->description('تحكم في الموضع والخط واللون لكل سطر نصي للإناث')
                                    ->schema([
                                        ...self::createLinePositionFields('_female', 1, 'السطر الأول', 900),
                                        ...self::createLinePositionFields('_female', 2, 'السطر الثاني', 1000),
                                        ...self::createLinePositionFields('_female', 3, 'السطر الثالث', 1100),
                                        ...self::createLinePositionFields('_female', 4, 'السطر الرابع', 1200),
                                        ...self::createLinePositionFields('_female', 5, 'السطر الخامس', 1300),
                                        ...self::createLinePositionFields('_female', 6, 'السطر السادس', 1400),
                                    ]),

                                Forms\Components\Section::make('معاينة الشهادة (أنثى)')
                                    ->description('معاينة حية لمواضع العناصر للإناث')
                                    ->schema([
                                        Forms\Components\ViewField::make('certificate_preview_female')
                                            ->view('filament.forms.components.certificate-preview')
                                            ->viewData(['gender' => 'female'])
                                            ->dehydrated(false)
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Create text fields for lines
     */
    private static function createTextFields(string $gender, int $lineNum, string $label, string $default): array
    {
        $variablesHelper = self::getVariablesHelperText();
        return [
            Forms\Components\Section::make($label)
                ->schema([
                    Forms\Components\Textarea::make("line{$lineNum}_text_{$gender}")
                        ->label('النص')
                        ->default($default)
                        ->rows(2)
                        ->helperText('المتغيرات المتاحة: ' . $variablesHelper)
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ];
    }

    /**
     * Create line position and style fields with sliders
     */
    private static function createLinePositionFields(string $suffix, int $lineNum, string $label, int $defaultY): array
    {
        return [
            Forms\Components\Section::make($label)
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make("line{$lineNum}_position_x{$suffix}")
                                ->label('الموضع الأفقي X')
                                ->numeric()
                                ->default(1240)
                                ->suffix('px')
                                ->minValue(0)
                                ->maxValue(2480)
                                ->live()
                                ->afterStateUpdated(fn ($state) => $state),
                            Forms\Components\TextInput::make("line{$lineNum}_position_y{$suffix}")
                                ->label('الموضع الرأسي Y')
                                ->numeric()
                                ->default($defaultY)
                                ->suffix('px')
                                ->minValue(0)
                                ->maxValue(1754)
                                ->live()
                                ->afterStateUpdated(fn ($state) => $state),
                        ]),
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Select::make("line{$lineNum}_font_family{$suffix}")
                                ->label('نوع الخط')
                                ->options(CertificateSetting::getFontOptions())
                                ->default('Cairo')
                                ->native(false),
                            Forms\Components\TextInput::make("line{$lineNum}_font_size{$suffix}")
                                ->label('حجم الخط')
                                ->numeric()
                                ->default(50)
                                ->suffix('px')
                                ->minValue(10)
                                ->maxValue(150)
                                ->live()
                                ->afterStateUpdated(fn ($state) => $state),
                            Forms\Components\ColorPicker::make("line{$lineNum}_color{$suffix}")
                                ->label('اللون')
                                ->default('#374151'),
                        ]),
                ])
                ->collapsible()
                ->collapsed($lineNum > 4)
                ->compact(),
        ];
    }

    /**
     * Get helper text for available variables
     */
    private static function getVariablesHelperText(): string
    {
        return collect(CertificateSetting::getAvailableVariables())
            ->keys()
            ->implode(' - ');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم القالب')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('page_title')
                    ->label('عنوان الصفحة')
                    ->limit(40),
                Tables\Columns\ImageColumn::make('background_image')
                    ->label('الخلفية')
                    ->disk('public')
                    ->width(100)
                    ->height(70),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->label('معاينة')
                    ->icon('heroicon-o-eye')
                    ->url(fn () => route('certificate.index'))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListCertificateSettings::route('/'),
            'create' => Pages\CreateCertificateSetting::route('/create'),
            'edit' => Pages\EditCertificateSetting::route('/{record}/edit'),
        ];
    }
}
