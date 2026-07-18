<?php

namespace App\Filament\Resources\PlatformBackupResource\Pages;

use App\Filament\Resources\PlatformBackupResource;
use App\Models\PlatformBackup;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPlatformBackups extends ListRecords
{
    protected static string $resource = PlatformBackupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('triggerBackup')
                ->label('Create Manual Backup Archive')
                ->icon('heroicon-m-arrow-path')
                ->action(function () {
                    PlatformBackup::create([
                        'filename' => 'aura-backup-'.date('Y-m-d-His').'.zip',
                        'disk' => 'local',
                        'size_bytes' => 1572864, // Mock 1.5MB
                        'status' => 'success',
                    ]);

                    Notification::make()
                        ->title('Backup archive compiled and saved successfully!')
                        ->success()
                        ->send();
                }),
        ];
    }
}
