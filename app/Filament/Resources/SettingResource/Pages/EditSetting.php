<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle boolean toggle value
        if ($this->record->type === 'boolean') {
            $boolVal = $data['bool_value'] ?? false;
            $data['value'] = $boolVal ? '1' : '0';
            unset($data['bool_value']);
        }
        
        // Handle image upload - FileUpload returns array, we need string
        if ($this->record->type === 'image' && isset($data['image_value'])) {
            $imageValue = $data['image_value'];
            // Get first value from array (or use as-is if already string)
            if (is_array($imageValue)) {
                $data['value'] = $imageValue[0] ?? $this->record->value;
            } else {
                $data['value'] = $imageValue ?: $this->record->value;
            }
            unset($data['image_value']);
        }
        
        return $data;
    }
}
