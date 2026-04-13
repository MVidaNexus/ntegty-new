<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdSlotResource\Pages;
use App\Models\AdSlot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class AdSlotResource extends Resource
{
    protected static ?string $model = AdSlot::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationLabel = 'أماكن الإعلانات';
    protected static ?string $navigationGroup = 'إعدادات الإعلانات';
    protected static ?string $pluralModelLabel = 'أماكن الإعلانات';
    protected static ?string $modelLabel = 'مكان إعلان';
    protected static ?int $navigationSort = 101;
    protected static ?string $slug = 'ad-slots';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('AdSlot')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('المعلومات الأساسية')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('اسم المكان')
                                            ->required()
                                            ->placeholder('مثال: إعلان الهيدر الرئيسي')
                                            ->maxLength(255),
                                        
                                        Forms\Components\TextInput::make('slug')
                                            ->label('المعرف الفريد')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->placeholder('مثال: header-main-ad')
                                            ->helperText('يستخدم في الكود لتحديد المكان')
                                            ->maxLength(255),
                                        
                                        Forms\Components\Select::make('page_type')
                                            ->label('نوع الصفحة')
                                            ->options(AdSlot::PAGE_TYPES)
                                            ->required()
                                            ->native(false)
                                            ->searchable(),
                                        
                                        Forms\Components\Select::make('position')
                                            ->label('موقع الإعلان')
                                            ->options(AdSlot::POSITIONS)
                                            ->required()
                                            ->native(false)
                                            ->searchable(),
                                        
                                        Forms\Components\Textarea::make('description')
                                            ->label('وصف المكان')
                                            ->placeholder('وصف اختياري للمكان')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('إعدادات الإعلان')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('ad_format')
                                            ->label('نوع الإعلان')
                                            ->options(AdSlot::AD_FORMATS)
                                            ->default('auto')
                                            ->required()
                                            ->native(false)
                                            ->live(),
                                        
                                        Forms\Components\TextInput::make('slot_id')
                                            ->label('Ad Slot ID')
                                            ->placeholder('1234567890')
                                            ->helperText('معرف وحدة الإعلان من AdSense (اختياري)')
                                            ->hidden(fn ($get) => $get('ad_format') === 'custom'),
                                        
                                        Forms\Components\TextInput::make('custom_channel')
                                            ->label('القناة المخصصة')
                                            ->placeholder('مثال: homepage_header')
                                            ->helperText('لتتبع أداء هذا المكان'),
                                        
                                        Forms\Components\TextInput::make('ad_layout')
                                            ->label('Ad Layout Key')
                                            ->placeholder('-fb')
                                            ->helperText('للإعلانات من نوع In-Feed')
                                            ->visible(fn ($get) => $get('ad_format') === 'in-feed'),
                                        
                                        Forms\Components\Textarea::make('custom_code')
                                            ->label('كود الإعلان المخصص')
                                            ->placeholder('<!-- ضع كود الإعلان هنا -->')
                                            ->rows(8)
                                            ->columnSpanFull()
                                            ->visible(fn ($get) => $get('ad_format') === 'custom'),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('العرض والظهور')
                            ->icon('heroicon-o-device-phone-mobile')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('تفعيل الإعلان')
                                            ->default(true)
                                            ->helperText('عند التعطيل، لن يظهر الإعلان'),
                                        
                                        Forms\Components\Toggle::make('show_on_mobile')
                                            ->label('إظهار على الموبايل')
                                            ->default(true),
                                        
                                        Forms\Components\Toggle::make('show_on_desktop')
                                            ->label('إظهار على الكمبيوتر')
                                            ->default(true),
                                        
                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('ترتيب العرض')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('الأرقام الأصغر تظهر أولاً'),
                                        
                                        Forms\Components\Textarea::make('custom_style')
                                            ->label('CSS مخصص')
                                            ->placeholder('margin: 10px auto; max-width: 728px;')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->weight(FontWeight::Bold),
                
                Tables\Columns\TextColumn::make('page_type')
                    ->label('نوع الصفحة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => AdSlot::PAGE_TYPES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'home' => 'success',
                        'country' => 'info',
                        'governorate' => 'warning',
                        'result' => 'danger',
                        'global' => 'gray',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('position')
                    ->label('الموقع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => AdSlot::POSITIONS[$state] ?? $state)
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('ad_format')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state): string => AdSlot::AD_FORMATS[$state] ?? $state),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعّل')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('show_on_mobile')
                    ->label('موبايل')
                    ->boolean()
                    ->trueIcon('heroicon-o-device-phone-mobile')
                    ->falseIcon('heroicon-o-device-phone-mobile')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\IconColumn::make('show_on_desktop')
                    ->label('كمبيوتر')
                    ->boolean()
                    ->trueIcon('heroicon-o-computer-desktop')
                    ->falseIcon('heroicon-o-computer-desktop')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('page_type')
                    ->label('نوع الصفحة')
                    ->options(AdSlot::PAGE_TYPES),
                
                Tables\Filters\SelectFilter::make('position')
                    ->label('الموقع')
                    ->options(AdSlot::POSITIONS),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('مفعّل')
                    ->falseLabel('معطّل'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle')
                        ->label(fn (AdSlot $record) => $record->is_active ? 'تعطيل' : 'تفعيل')
                        ->icon(fn (AdSlot $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                        ->color(fn (AdSlot $record) => $record->is_active ? 'danger' : 'success')
                        ->action(fn (AdSlot $record) => $record->update(['is_active' => !$record->is_active])),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('تفعيل المحدد')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('تعطيل المحدد')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdSlots::route('/'),
            'create' => Pages\CreateAdSlot::route('/create'),
            'edit' => Pages\EditAdSlot::route('/{record}/edit'),
        ];
    }
}
