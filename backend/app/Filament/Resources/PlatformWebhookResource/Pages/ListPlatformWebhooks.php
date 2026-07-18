<?php

namespace App\Filament\Resources\PlatformWebhookResource\Pages;

use App\Filament\Resources\PlatformWebhookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlatformWebhooks extends ListRecords
{
    protected static string $resource = PlatformWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
