<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialLinkResource\Pages;
use App\Models\SocialLink;
use App\Models\Country;
use App\Models\ExamType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;
    protected static ?string $navigationIcon = 'heroicon-o-share';
    protected static ?string $navigationLabel = 'السوشيال حسب الصفحة';
    protected static ?string $modelLabel = 'رابط سوشيال';
    protected static ?string $pluralModelLabel = 'روابط السوشيال حسب الصفحة';
    protected static ?string $navigationGroup = 'إعدادات السوشيال';
    protected static ?int $navigationSort = 51;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('scope_type', ['country', 'exam_type']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الرابط')
                    ->description('حدد المنصة والرابط')
                    ->icon('heroicon-o-link')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('platform')
                                    ->label('المنصة')
                                    ->options(SocialLink::getPlatformOptions())
                                    ->required()
                                    ->native(false)
                                    ->searchable(),
                                    
                                Forms\Components\TextInput::make('url')
                                    ->label('الرابط')
                                    ->url()
                                    ->required()
                                    ->placeholder('https://...')
                                    ->suffixIcon('heroicon-o-link'),
                            ]),
                            
                        Forms\Components\TextInput::make('label')
                            ->label('تسمية مخصصة (اختياري)')
                            ->placeholder('اتركه فارغاً لاستخدام اسم المنصة')
                            ->maxLength(50),
                    ]),

                Forms\Components\Section::make('نطاق الظهور')
                    ->description('حدد أين سيظهر هذا الرابط')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Radio::make('scope_type')
                            ->label('نوع النطاق')
                            ->options([
                                'country' => '🏳️ دولة كاملة - يظهر في جميع صفحات الدولة',
                                'exam_type' => '📚 شهادة محددة - يظهر في صفحة شهادة معينة فقط',
                            ])
                            ->required()
                            ->default('country')
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('scope_id')
                            ->label(fn (Forms\Get $get) => $get('scope_type') === 'exam_type' ? 'اختر الشهادة' : 'اختر الدولة')
                            ->options(function (Forms\Get $get) {
                                if ($get('scope_type') === 'exam_type') {
                                    return ExamType::with('country')
                                        ->get()
                                        ->mapWithKeys(fn ($e) => [
                                            $e->id => $e->name_ar . ' - ' . ($e->country?->name_ar ?? '')
                                        ]);
                                }
                                return Country::pluck('name_ar', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->preload()
                            ->live()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('الإعدادات')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('ترتيب الظهور')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('الأرقام الأصغر تظهر أولاً'),
                                    
                                Forms\Components\Toggle::make('is_active')
                                    ->label('مفعّل')
                                    ->default(true),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('platform')
                    ->label('المنصة')
                    ->formatStateUsing(fn ($state) => SocialLink::$platforms[$state]['name'] ?? $state)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('url')
                    ->label('الرابط')
                    ->limit(35)
                    ->url(fn ($state) => $state, true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('scope_type')
                    ->label('النطاق')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'country' => 'دولة',
                        'exam_type' => 'شهادة',
                        default => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'country' => 'info',
                        'exam_type' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('scope_name')
                    ->label('الصفحة')
                    ->getStateUsing(fn ($record) => $record->getScopeName())
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعّل')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->label('المنصة')
                    ->options(SocialLink::getPlatformOptions()),

                Tables\Filters\SelectFilter::make('scope_type')
                    ->label('نوع النطاق')
                    ->options([
                        'country' => 'دولة',
                        'exam_type' => 'شهادة',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialLinks::route('/'),
            'create' => Pages\CreateSocialLink::route('/create'),
            'edit' => Pages\EditSocialLink::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('scope_type', ['country', 'exam_type'])->count() ?: null;
    }
}
