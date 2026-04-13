<?php

namespace App\Filament\Pages;

use App\Models\AdSlot;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Filament\Tables\Table;

class GovernorateAdsPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'إعلانات المحافظات';
    protected static ?string $navigationGroup = 'إعدادات الإعلانات';
    protected static ?int $navigationSort = 104;
    protected static string $view = 'filament.pages.ads-page';
    protected static ?string $title = 'إعلانات صفحات المحافظات';
    protected static ?string $slug = 'governorate-ads';

    public function table(Table $table): Table
    {
        return $table
            ->query(AdSlot::query()->where('page_type', 'governorate')->orderBy('sort_order'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المكان')
                    ->searchable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('position')
                    ->label('الموقع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => AdSlot::POSITIONS[$state] ?? $state)
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('ad_format')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state): string => AdSlot::AD_FORMATS[$state] ?? $state),
                
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('مفعّل'),
                
                Tables\Columns\ToggleColumn::make('show_on_mobile')
                    ->label('موبايل'),
                
                Tables\Columns\ToggleColumn::make('show_on_desktop')
                    ->label('كمبيوتر'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Select::make('ad_format')
                            ->label('نوع الإعلان')
                            ->options(AdSlot::AD_FORMATS)
                            ->required(),
                        TextInput::make('slot_id')
                            ->label('Ad Slot ID'),
                        TextInput::make('custom_channel')
                            ->label('القناة المخصصة'),
                        Toggle::make('is_active')
                            ->label('تفعيل'),
                        Toggle::make('show_on_mobile')
                            ->label('إظهار على الموبايل'),
                        Toggle::make('show_on_desktop')
                            ->label('إظهار على الكمبيوتر'),
                        Textarea::make('custom_code')
                            ->label('كود مخصص')
                            ->rows(5),
                    ]),
            ])
            ->heading('📍 أماكن الإعلانات في صفحات المحافظات')
            ->description('تحكم في الإعلانات التي تظهر في صفحات المحافظات');
    }
}
