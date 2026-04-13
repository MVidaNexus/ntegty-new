<?php

namespace App\Filament\Pages;

use App\Models\Country;
use App\Models\ExamType;
use App\Models\Governorate;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class ManageGovernorateResults extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'ملفات المحافظات';
    protected static ?string $title = 'إدارة ملفات نتائج المحافظات';
    protected static ?string $navigationGroup = 'النتائج';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.manage-governorate-results';
    
    protected static ?string $slug = 'manage-governorate-results';

    public ?int $selectedCountryId = null;
    public ?int $selectedExamTypeId = null;
    public array $governoratesData = [];

    public function mount(): void
    {
        // Check if country_id passed in URL
        $countryId = request()->query('country_id');
        if ($countryId) {
            $this->selectedCountryId = (int) $countryId;
            $this->loadGovernorates();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اختر الدولة')
                    ->description('حدد الدولة لعرض محافظاتها')
                    ->schema([
                        Select::make('selectedCountryId')
                            ->label('الدولة')
                            ->options(Country::pluck('name_ar', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => $this->loadGovernorates())
                            ->prefixIcon('heroicon-o-flag')
                            ->placeholder('اختر الدولة...'),
                    ])
                    ->columns(1),
            ])
            ->statePath('');
    }

    public function loadGovernorates(): void
    {
        if (!$this->selectedCountryId) {
            $this->governoratesData = [];
            return;
        }

        $governorates = Governorate::where('country_id', $this->selectedCountryId)
            ->orderBy('name_ar')
            ->get();

        $this->governoratesData = $governorates->map(fn ($gov) => [
            'id' => $gov->id,
            'name_ar' => $gov->name_ar,
            'is_declared' => $gov->is_declared,
            'result_pdf_path' => $gov->result_pdf_path,
            'has_pdf' => $gov->hasResultPdf(),
        ])->toArray();
    }

    public function toggleDeclared(int $governorateId): void
    {
        $governorate = Governorate::find($governorateId);
        if ($governorate) {
            $governorate->update(['is_declared' => !$governorate->is_declared]);
            $this->loadGovernorates();
            
            Notification::make()
                ->title($governorate->is_declared ? 'تم اعتماد النتيجة' : 'تم إلغاء الاعتماد')
                ->success()
                ->send();
        }
    }

    public function deletePdf(int $governorateId): void
    {
        $governorate = Governorate::find($governorateId);
        if ($governorate && $governorate->result_pdf_path) {
            // Delete the file
            $filePath = public_path('uploads/' . $governorate->result_pdf_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $governorate->update(['result_pdf_path' => null]);
            $this->loadGovernorates();
            
            Notification::make()
                ->title('تم حذف الملف')
                ->success()
                ->send();
        }
    }

    public function declareAll(): void
    {
        Governorate::where('country_id', $this->selectedCountryId)
            ->update(['is_declared' => true]);
        
        $this->loadGovernorates();
        
        Notification::make()
            ->title('تم اعتماد جميع المحافظات')
            ->success()
            ->send();
    }

    public function undeclareAll(): void
    {
        Governorate::where('country_id', $this->selectedCountryId)
            ->update(['is_declared' => false]);
        
        $this->loadGovernorates();
        
        Notification::make()
            ->title('تم إلغاء اعتماد جميع المحافظات')
            ->success()
            ->send();
    }
}
