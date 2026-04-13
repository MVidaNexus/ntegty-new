<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultScheduleResource\Pages;
use App\Models\ResultSchedule;
use App\Models\Country;
use App\Models\ExamType;
use App\Models\Governorate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResultScheduleResource extends Resource
{
    protected static ?string $model = ResultSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $modelLabel = 'موعد نتيجة';
    protected static ?string $pluralModelLabel = 'مواعيد النتائج';
    protected static ?string $navigationLabel = 'مواعيد النتائج';
    protected static ?string $navigationGroup = 'إدارة البيانات';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الموعد')
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->label('الدولة')
                            ->options(Country::where('is_active', true)->pluck('name_ar', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('governorate_id', null)),
                            
                        Forms\Components\Select::make('exam_type_id')
                            ->label('نوع الشهادة')
                            ->options(fn (Get $get) => 
                                ExamType::when($get('country_id'), function ($query, $countryId) {
                                    return $query->where('country_id', $countryId);
                                })->pluck('name_ar', 'id')
                            )
                            ->placeholder('كل الشهادات')
                            ->helperText('اتركه فارغاً ليشمل كل الشهادات'),
                            
                        Forms\Components\Select::make('governorate_id')
                            ->label('المحافظة')
                            ->options(fn (Get $get) => 
                                Governorate::when($get('country_id'), function ($query, $countryId) {
                                    return $query->where('country_id', $countryId);
                                })->pluck('name_ar', 'id')
                            )
                            ->placeholder('كل المحافظات')
                            ->searchable()
                            ->helperText('اتركه فارغاً ليشمل كل المحافظات'),
                            
                        Forms\Components\DateTimePicker::make('expected_date')
                            ->label('الموعد المتوقع')
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d H:i')
                            ->seconds(false),
                            
                        Forms\Components\Textarea::make('note')
                            ->label('ملاحظة')
                            ->placeholder('ملاحظة تظهر للمستخدمين (اختياري)')
                            ->rows(2),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true)
                            ->helperText('إلغاء التفعيل يخفي هذا الموعد'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name_ar')
                    ->label('الدولة')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('examType.name_ar')
                    ->label('الشهادة')
                    ->default('كل الشهادات')
                    ->badge()
                    ->color(fn ($record) => $record->exam_type_id ? 'primary' : 'gray'),
                    
                Tables\Columns\TextColumn::make('governorate.name_ar')
                    ->label('المحافظة')
                    ->default('كل المحافظات')
                    ->badge()
                    ->color(fn ($record) => $record->governorate_id ? 'success' : 'gray'),
                    
                Tables\Columns\TextColumn::make('expected_date')
                    ->label('الموعد المتوقع')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->expected_date->isPast() ? 'danger' : 'success'),
                    
                Tables\Columns\IconColumn::make('governorate.is_declared')
                    ->label('حالة النتيجة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->getStateUsing(fn ($record) => $record->governorate?->is_declared ?? false),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->defaultSort('expected_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('country_id')
                    ->label('الدولة')
                    ->options(Country::where('is_active', true)->pluck('name_ar', 'id'))
                    ->searchable(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->trueLabel('نشط')
                    ->falseLabel('غير نشط')
                    ->placeholder('الكل'),
                    
                Tables\Filters\Filter::make('upcoming')
                    ->label('مواعيد قادمة')
                    ->query(fn (Builder $query) => $query->where('expected_date', '>', now())),
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
            'index' => Pages\ListResultSchedules::route('/'),
            'create' => Pages\CreateResultSchedule::route('/create'),
            'edit' => Pages\EditResultSchedule::route('/{record}/edit'),
        ];
    }
}
