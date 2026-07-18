<?php

namespace App\Filament\Resources\PlatformBackupResource\Pages;

use App\Filament\Resources\PlatformBackupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlatformBackup extends EditRecord
{
    protected static string $resource = PlatformBackupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
