<?php

namespace App\Filament\Resources\HomepageLayoutResource\Pages;

use App\Filament\Resources\HomepageLayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomepageLayout extends EditRecord
{
    protected static string $resource = HomepageLayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
