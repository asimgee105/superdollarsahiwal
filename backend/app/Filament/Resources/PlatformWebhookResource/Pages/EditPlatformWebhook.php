<?php

namespace App\Filament\Resources\PlatformWebhookResource\Pages;

use App\Filament\Resources\PlatformWebhookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlatformWebhook extends EditRecord
{
    protected static string $resource = PlatformWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
