<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Guess mime type and size if uploaded
        if (isset($data['path'])) {
            $data['mime_type'] = 'image/jpeg';
            $data['size'] = 102400; // placeholder size (100KB)
        }

        return $data;
    }
}
