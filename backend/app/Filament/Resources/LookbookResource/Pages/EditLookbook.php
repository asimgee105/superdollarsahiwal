<?php

namespace App\Filament\Resources\LookbookResource\Pages;

use App\Filament\Resources\LookbookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLookbook extends EditRecord
{
    protected static string $resource = LookbookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
