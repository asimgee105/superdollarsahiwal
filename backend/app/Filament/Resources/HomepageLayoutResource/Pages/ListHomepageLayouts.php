<?php

namespace App\Filament\Resources\HomepageLayoutResource\Pages;

use App\Filament\Resources\HomepageLayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomepageLayouts extends ListRecords
{
    protected static string $resource = HomepageLayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
