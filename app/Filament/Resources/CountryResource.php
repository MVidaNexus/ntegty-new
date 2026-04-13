<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $modelLabel = 'دولة';
    protected static ?string $pluralModelLabel = 'الدول';
    protected static ?string $navigationLabel = 'الدول';
    protected static ?string $navigationGroup = 'إدارة البيانات';
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('البيانات الأساسية')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Section::make('معلومات الدولة')
                                    ->schema([
                                        Forms\Components\TextInput::make('name_ar')
                                            ->label('اسم الدولة (عربي)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('name_en')
                                            ->label('اسم الدولة (إنجليزي)')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('code')
                                            ->label('كود الدولة (ISO)')
                                            ->required()
                                            ->maxLength(3),
                                        Forms\Components\TextInput::make('telegram_url')
                                            ->label('رابط تليجرام')
                                            ->url()
                                            ->maxLength(255),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('تفعيل')
                                            ->default(true)
                                            ->required(),
                                    ])->columns(2),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\Section::make('إعدادات محركات البحث (SEO)')
                                    ->description('هذه الإعدادات تظهر في نتائج بحث جوجل ومحركات البحث الأخرى')
                                    ->schema([
                                        Forms\Components\TextInput::make('seo_title')
                                            ->label('عنوان الصفحة (Title)')
                                            ->placeholder('نتائج امتحانات {اسم الدولة} | نتيجتي')
                                            ->maxLength(70)
                                            ->helperText('يظهر في تاب المتصفح ونتائج البحث (يفضل 60-70 حرف). اترك فارغاً للعنوان التلقائي'),
                                        
                                        Forms\Components\Textarea::make('seo_description')
                                            ->label('وصف الصفحة (Meta Description)')
                                            ->placeholder('نتائج امتحانات الشهادات في {اسم الدولة}...')
                                            ->maxLength(160)
                                            ->rows(2)
                                            ->helperText('يظهر أسفل العنوان في نتائج البحث (يفضل 150-160 حرف)'),
                                        
                                        Forms\Components\Textarea::make('seo_keywords')
                                            ->label('الكلمات المفتاحية (Keywords)')
                                            ->placeholder('نتائج الامتحانات, نتيجة الشهادة الإعدادية, نتيجتي, رقم الجلوس')
                                            ->rows(2)
                                            ->helperText('أضف كلمات مفتاحية مفصولة بفاصلة (,)'),
                                    ]),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('المحتوى')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('صندوق المحتوى')
                                    ->description('محتوى يظهر في صفحة الدولة لتحسين SEO وتجربة المستخدم')
                                    ->schema([
                                        Forms\Components\Toggle::make('show_content_section')
                                            ->label('تفعيل صندوق المحتوى')
                                            ->helperText('إظهار/إخفاء صندوق المحتوى في صفحة الدولة')
                                            ->default(true),
                                        
                                        Forms\Components\TextInput::make('content_title')
                                            ->label('عنوان الصندوق')
                                            ->placeholder('معلومات عن نتائج الامتحانات')
                                            ->maxLength(255),
                                        
                                        Forms\Components\Textarea::make('content_intro')
                                            ->label('مقدمة قصيرة')
                                            ->placeholder('نبذة مختصرة...')
                                            ->rows(2),
                                        
                                        \FilamentTiptapEditor\TiptapEditor::make('content_body')
                                            ->label('المحتوى الرئيسي')
                                            ->placeholder('اكتب محتوى عن نتائج امتحانات هذه الدولة...')
                                            ->profile('default')
                                            ->tools([
                                                'heading',
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'link',
                                                'bullet-list',
                                                'ordered-list',
                                                'blockquote',
                                                'hr',
                                                'table',
                                                'media',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('نشط')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
