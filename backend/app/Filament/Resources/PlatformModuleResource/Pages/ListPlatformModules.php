<?php

namespace App\Filament\Resources\PlatformModuleResource\Pages;

use App\Filament\Resources\PlatformModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlatformModules extends ListRecords
{
    protected static string $resource = PlatformModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
