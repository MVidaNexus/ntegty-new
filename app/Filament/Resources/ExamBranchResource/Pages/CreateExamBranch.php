<?php

namespace App\Filament\Resources\ExamBranchResource\Pages;

use App\Filament\Resources\ExamBranchResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExamBranch extends CreateRecord
{
    protected static string $resource = ExamBranchResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
