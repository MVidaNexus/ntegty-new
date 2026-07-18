<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PreRegistrationResource\Pages;
use App\Filament\Resources\PreRegistrationResource\RelationManagers;
use App\Models\PreRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreRegistrationResource extends Resource
{
    protected static ?string $model = PreRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'النتائج والطلاب';
    protected static ?string $modelLabel = 'تسجيل مسبق';
    protected static ?string $pluralModelLabel = 'التسجيل المسبق للنتائج';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('اسم الطالب')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('رقم الهاتف / واتساب')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('exam_type_slug')
                    ->label('نوع الشهادة')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('seat_number')
                    ->label('رقم الجلوس')
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('exam_type_slug')
                    ->label('الشهادة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('governorate_slug')
                    ->label('المحافظة')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('seat_number')
                    ->label('رقم الجلوس')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPreRegistrations::route('/'),
            'create' => Pages\CreatePreRegistration::route('/create'),
            'edit' => Pages\EditPreRegistration::route('/{record}/edit'),
        ];
    }
}
