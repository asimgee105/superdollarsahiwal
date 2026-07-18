<?php

namespace App\Filament\Resources\PlatformThemeResource\Pages;

use App\Filament\Resources\PlatformThemeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlatformThemes extends ListRecords
{
    protected static string $resource = PlatformThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
