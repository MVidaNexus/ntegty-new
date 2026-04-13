<?php

namespace App\Filament\Resources\ExamBranchResource\Pages;

use App\Filament\Resources\ExamBranchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamBranches extends ListRecords
{
    protected static string $resource = ExamBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
