<?php

namespace App\Filament\Resources\LookbookResource\Pages;

use App\Filament\Resources\LookbookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLookbooks extends ListRecords
{
    protected static string $resource = LookbookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
