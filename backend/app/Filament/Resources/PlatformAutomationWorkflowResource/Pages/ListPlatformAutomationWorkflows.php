<?php

namespace App\Filament\Resources\PlatformAutomationWorkflowResource\Pages;

use App\Filament\Resources\PlatformAutomationWorkflowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlatformAutomationWorkflows extends ListRecords
{
    protected static string $resource = PlatformAutomationWorkflowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
