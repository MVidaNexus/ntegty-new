<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Models\Country;
use App\Models\ExamType;
use App\Models\ExamBranch;
use App\Models\Governorate;
use App\Models\Result;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'نتيجة';
    protected static ?string $pluralModelLabel = 'النتائج';
    protected static ?string $navigationLabel = 'النتائج';
    protected static ?string $navigationGroup = 'النتائج';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الطالب')
                    ->schema([
                        Forms\Components\TextInput::make('student_name')
                            ->label('اسم الطالب')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('seat_number')
                            ->label('رقم الجلوس')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('exam_type_id')
                            ->label('نوع الشهادة')
                            ->relationship('examType', 'name_ar')
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('branch_id', null))
                            ->required(),
                        Forms\Components\Select::make('branch_id')
                            ->label('الشعبة/النظام')
                            ->options(function (Get $get) {
                                $examTypeId = $get('exam_type_id');
                                if (!$examTypeId) return [];
                                return ExamBranch::where('exam_type_id', $examTypeId)
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->pluck('name_ar', 'id');
                            })
                            ->visible(function (Get $get) {
                                $examTypeId = $get('exam_type_id');
                                if (!$examTypeId) return false;
                                return ExamBranch::where('exam_type_id', $examTypeId)->where('is_active', true)->exists();
                            })
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('system_type')
                            ->label('نظام الدراسة')
                            ->options([
                                'old' => 'نظام قديم',
                                'new' => 'نظام حديث',
                            ])
                            ->visible(function (Get $get) {
                                $examTypeId = $get('exam_type_id');
                                if (!$examTypeId) return false;
                                $examType = ExamType::find($examTypeId);
                                return $examType && str_contains($examType->code, 'secondary');
                            }),
                        Forms\Components\Select::make('semester')
                            ->label('الفصل الدراسي')
                            ->options(Result::getSemesterOptions())
                            ->default(Result::SEMESTER_BOTH)
                            ->visible(function (Get $get) {
                                $examTypeId = $get('exam_type_id');
                                if (!$examTypeId) return false;
                                $examType = ExamType::find($examTypeId);
                                // Show for preparatory and primary exams only
                                return $examType && (str_contains($examType->code, 'preparatory') || str_contains($examType->code, 'primary'));
                            }),
                        Forms\Components\Select::make('governorate_id')
                            ->label('المحافظة')
                            ->relationship('governorate', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->visible(function (Get $get) {
                                $examTypeId = $get('exam_type_id');
                                if ($examTypeId) {
                                    $examType = \App\Models\ExamType::find($examTypeId);
                                    if ($examType && (str_contains($examType->code, 'secondary') || str_contains($examType->code, 'diploma'))) {
                                        return false;
                                    }
                                }
                                return true;
                            })
                            ->required(),
                        Forms\Components\Select::make('academic_year_id')
                            ->label('السنة الدراسية')
                            ->relationship('academicYear', 'year')
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('النتيجة')
                    ->schema([
                        Forms\Components\TextInput::make('total_score')
                            ->label('المجموع الكلي')
                            ->numeric()
                            ->required(),
                        Forms\Components\KeyValue::make('subjects_data')
                            ->label('المواد والدرجات')
                            ->keyLabel('المادة')
                            ->valueLabel('الدرجة')
                            ->reorderable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['examType', 'branch', 'governorate', 'academicYear']))
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('seat_number')
                    ->label('رقم الجلوس')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('student_name')
                    ->label('اسم الطالب')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_score')
                    ->label('المجموع')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('examType.name_ar')
                    ->label('الشهادة')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('branch.name_ar')
                    ->label('الشعبة')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('غير محدد'),
                Tables\Columns\TextColumn::make('system_type')
                    ->label('النظام')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'old' => 'نظام قديم',
                        'new' => 'نظام حديث',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('governorate.name_ar')
                    ->label('المحافظة')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('academicYear.year')
                    ->label('السنة')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الرفع')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('criteria')
                    ->form([
                        Forms\Components\Select::make('academic_year_id')
                            ->label('السنة الدراسية')
                            ->relationship('academicYear', 'year')
                            ->preload()
                            ->searchable()
                            ->default(fn () => \App\Models\AcademicYear::where('is_active', true)->value('id')),

                        Forms\Components\Select::make('country_id')
                            ->label('الدولة')
                            ->options(Country::pluck('name_ar', 'id'))
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('exam_type_id', null);
                                $set('governorate_id', null);
                            })
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('exam_type_id')
                            ->label('نوع الشهادة')
                            ->options(fn (Get $get) => ExamType::where('country_id', $get('country_id'))->pluck('name_ar', 'id'))
                            ->visible(fn (Get $get) => $get('country_id'))
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('branch_id', null))
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('branch_id')
                            ->label('الشعبة/النظام')
                            ->options(function (Get $get) {
                                $examTypeId = $get('exam_type_id');
                                if (!$examTypeId) return [];
                                return ExamBranch::where('exam_type_id', $examTypeId)
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->pluck('name_ar', 'id');
                            })
                            ->visible(function (Get $get) {
                                $examTypeId = $get('exam_type_id');
                                if (!$examTypeId) return false;
                                return ExamBranch::where('exam_type_id', $examTypeId)->where('is_active', true)->exists();
                            })
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('system_type')
                            ->label('نظام الدراسة')
                            ->options([
                                'old' => 'نظام قديم',
                                'new' => 'نظام حديث',
                            ])
                            ->visible(function (Get $get) {
                                $examTypeId = $get('exam_type_id');
                                if ($examTypeId) {
                                    $examType = \App\Models\ExamType::find($examTypeId);
                                    if ($examType && str_contains($examType->code, 'secondary')) {
                                        return true;
                                    }
                                }
                                return false;
                            }),

                        Forms\Components\Select::make('governorate_id')
                            ->label('المحافظة')
                            ->options(fn (Get $get) => Governorate::where('country_id', $get('country_id'))->pluck('name_ar', 'id'))
                            ->visible(function (Get $get) {
                                if (!$get('country_id')) return false;
                                
                                $examTypeId = $get('exam_type_id');
                                if ($examTypeId) {
                                    $examType = \App\Models\ExamType::find($examTypeId);
                                    // Hide governorate for unified exams (Secondary, Diplomas)
                                    if ($examType && (str_contains($examType->code, 'secondary') || str_contains($examType->code, 'diploma'))) {
                                        return false;
                                    }
                                }
                                return true;
                            })
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        // If no country is selected, don't show any results
                        if (empty($data['country_id'])) {
                            return $query->whereRaw('1 = 0');
                        }

                        return $query
                            ->when(
                                $data['academic_year_id'],
                                fn (Builder $query, $date) => $query->where('academic_year_id', $date),
                            )
                            ->when(
                                $data['exam_type_id'],
                                fn (Builder $query, $date) => $query->where('exam_type_id', $date),
                            )
                            ->when(
                                $data['branch_id'] ?? null,
                                fn (Builder $query, $branchId) => $query->where('branch_id', $branchId),
                            )
                            ->when(
                                $data['system_type'] ?? null,
                                fn (Builder $query, $type) => $query->where('system_type', $type),
                            )
                            ->when(
                                $data['governorate_id'],
                                fn (Builder $query, $date) => $query->where('governorate_id', $date),
                            )
                            ->when(
                                $data['country_id'] && !$data['exam_type_id'] && !$data['governorate_id'],
                                fn (Builder $query) => $query->whereHas('examType', fn ($q) => $q->where('country_id', $data['country_id'])),
                            );
                    })
                    ->columns(4)
                    ->columnSpanFull(),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_update')
                        ->label('تعديل البيانات (نقل)')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->form([
                            Forms\Components\Section::make('بيانات النقل')
                                ->description('حدد البيانات الجديدة التي تريد تطبيقها على النتائج المختارة. اترك الحقل فارغاً إذا كنت لا تريد تغييره.')
                                ->schema([
                                    Forms\Components\Select::make('academic_year_id')
                                        ->label('السنة الدراسية')
                                        ->relationship('academicYear', 'year')
                                        ->searchable()
                                        ->preload(),

                                    Forms\Components\Select::make('country_id')
                                        ->label('الدولة (لتحديد القوائم)')
                                        ->options(\App\Models\Country::pluck('name_ar', 'id'))
                                        ->live()
                                        ->afterStateUpdated(function (Set $set) {
                                            $set('exam_type_id', null);
                                            $set('governorate_id', null);
                                        }),

                                    Forms\Components\Select::make('exam_type_id')
                                        ->label('نوع الشهادة الجديد')
                                        ->options(fn (Get $get) => \App\Models\ExamType::where('country_id', $get('country_id'))->pluck('name_ar', 'id'))
                                        ->visible(fn (Get $get) => $get('country_id'))
                                        ->live()
                                        ->afterStateUpdated(fn (Set $set) => $set('branch_id', null))
                                        ->searchable()
                                        ->preload(),

                                    Forms\Components\Select::make('branch_id')
                                        ->label('الشعبة الجديدة')
                                        ->options(function (Get $get) {
                                            $examTypeId = $get('exam_type_id');
                                            if (!$examTypeId) return [];
                                            return ExamBranch::where('exam_type_id', $examTypeId)
                                                ->where('is_active', true)
                                                ->orderBy('sort_order')
                                                ->pluck('name_ar', 'id');
                                        })
                                        ->visible(function (Get $get) {
                                            $examTypeId = $get('exam_type_id');
                                            if (!$examTypeId) return false;
                                            return ExamBranch::where('exam_type_id', $examTypeId)->where('is_active', true)->exists();
                                        })
                                        ->searchable()
                                        ->preload(),

                                    Forms\Components\Select::make('governorate_id')
                                        ->label('المحافظة الجديدة')
                                        ->options(fn (Get $get) => \App\Models\Governorate::where('country_id', $get('country_id'))->pluck('name_ar', 'id'))
                                        ->visible(function (Get $get) {
                                            if (!$get('country_id')) return false;
                                            
                                            $examTypeId = $get('exam_type_id');
                                            if ($examTypeId) {
                                                $examType = \App\Models\ExamType::find($examTypeId);
                                                if ($examType && (str_contains($examType->code, 'secondary') || str_contains($examType->code, 'diploma'))) {
                                                    return false;
                                                }
                                            }
                                            return true;
                                        })
                                        ->searchable()
                                        ->preload(),
                                ])
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $updateData = [];
                            if (!empty($data['academic_year_id'])) $updateData['academic_year_id'] = $data['academic_year_id'];
                            if (!empty($data['exam_type_id'])) $updateData['exam_type_id'] = $data['exam_type_id'];
                            if (!empty($data['branch_id'])) $updateData['branch_id'] = $data['branch_id'];
                            if (!empty($data['governorate_id'])) $updateData['governorate_id'] = $data['governorate_id'];
                            
                            if (!empty($updateData)) {
                                // Use update query for performance
                                \App\Models\Result::whereIn('id', $records->pluck('id'))->update($updateData);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('تم تحديث السجلات المختارة بنجاح')
                                    ->success()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListResults::route('/'),
            'create' => Pages\CreateResult::route('/create'),
            'edit' => Pages\EditResult::route('/{record}/edit'),
        ];
    }
}
