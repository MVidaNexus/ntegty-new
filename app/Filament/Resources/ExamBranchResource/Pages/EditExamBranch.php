<?php

namespace App\Filament\Resources\ExamBranchResource\Pages;

use App\Filament\Resources\ExamBranchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamBranch extends EditRecord
{
    protected static string $resource = ExamBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
