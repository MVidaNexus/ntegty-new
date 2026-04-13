<?php

namespace App\Filament\Resources\UploadLogResource\Pages;

use App\Filament\Resources\UploadLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUploadLogs extends ListRecords
{
    protected static string $resource = UploadLogResource::class;

    // Auto-refresh every 5 seconds to show progress updates
    protected ?string $pollingInterval = '5s';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upload')
                ->label('رفع ملف جديد')
                ->url(\App\Filament\Pages\UploadResult::getUrl())
                ->button(),
        ];
    }
}
