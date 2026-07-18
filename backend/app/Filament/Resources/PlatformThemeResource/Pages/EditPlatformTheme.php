<?php

namespace App\Filament\Resources\PlatformThemeResource\Pages;

use App\Filament\Resources\PlatformThemeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlatformTheme extends EditRecord
{
    protected static string $resource = PlatformThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
