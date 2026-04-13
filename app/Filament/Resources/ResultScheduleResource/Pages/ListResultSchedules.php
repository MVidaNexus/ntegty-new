<?php

namespace App\Filament\Resources\ResultScheduleResource\Pages;

use App\Filament\Resources\ResultScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResultSchedules extends ListRecords
{
    protected static string $resource = ResultScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
