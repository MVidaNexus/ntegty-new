<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamBranchResource\Pages;
use App\Models\ExamBranch;
use App\Models\ExamType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExamBranchResource extends Resource
{
    protected static ?string $model = ExamBranch::class;

    protected static ?string $modelLabel = 'شُعبة/نظام';
    protected static ?string $pluralModelLabel = 'الشُعب والأنظمة';
    protected static ?string $navigationLabel = 'الشُعب والأنظمة';
    protected static ?string $navigationGroup = 'إدارة البيانات';
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الشُعبة')
                    ->schema([
                        Forms\Components\Select::make('exam_type_id')
                            ->label('نوع الشهادة')
                            ->options(ExamType::all()->pluck('name_ar', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),
                        
                        Forms\Components\TextInput::make('name_ar')
                            ->label('اسم الشُعبة (عربي)')
                            ->placeholder('مثال: علمي علوم، تجاري، صناعي 3 سنوات')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم الشُعبة (إنجليزي)')
                            ->placeholder('Example: Science, Commercial')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('code')
                            ->label('الكود')
                            ->placeholder('eg_secondary_science')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (للروابط)')
                            ->placeholder('science')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('المظهر')
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->label('أيقونة Font Awesome')
                            ->placeholder('fa-flask, fa-briefcase, fa-industry')
                            ->helperText('أدخل اسم الأيقونة من Font Awesome (بدون fa-solid)')
                            ->maxLength(100),
                        
                        Forms\Components\Select::make('color')
                            ->label('اللون')
                            ->options([
                                'blue' => '🔵 أزرق',
                                'green' => '🟢 أخضر',
                                'red' => '🔴 أحمر',
                                'yellow' => '🟡 أصفر',
                                'purple' => '🟣 بنفسجي',
                                'pink' => '🩷 وردي',
                                'orange' => '🟠 برتقالي',
                                'slate' => '⚫ رمادي',
                                'emerald' => '💚 زمردي',
                                'teal' => '🩵 تركواز',
                                'rose' => '🌹 وردي غامق',
                            ])
                            ->default('blue'),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتيب العرض')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('إعدادات الدرجات')
                    ->description('حدد المجموع الكلي ودرجة النجاح لهذه الشُعبة')
                    ->schema([
                        Forms\Components\TextInput::make('total_score')
                            ->label('المجموع الكلي')
                            ->helperText('المجموع الكلي لهذه الشُعبة')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('درجة'),
                        
                        Forms\Components\TextInput::make('passing_score')
                            ->label('درجة النجاح')
                            ->helperText('الحد الأدنى للنجاح')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('درجة'),
                    ])
                    ->columns(2),

                Forms\Components\Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['examType']))
            ->columns([
                Tables\Columns\TextColumn::make('examType.name_ar')
                    ->label('نوع الشهادة')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الشُعبة')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('total_score')
                    ->label('المجموع الكلي')
                    ->suffix(' درجة')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('passing_score')
                    ->label('درجة النجاح')
                    ->suffix(' درجة')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_type_id')
                    ->label('نوع الشهادة')
                    ->options(ExamType::all()->pluck('name_ar', 'id')),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة'),
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
            ->defaultSort('sort_order');
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
            'index' => Pages\ListExamBranches::route('/'),
            'create' => Pages\CreateExamBranch::route('/create'),
            'edit' => Pages\EditExamBranch::route('/{record}/edit'),
        ];
    }
}
