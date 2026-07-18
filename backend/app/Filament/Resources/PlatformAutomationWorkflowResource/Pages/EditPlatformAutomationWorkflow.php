<?php

namespace App\Filament\Resources\PlatformAutomationWorkflowResource\Pages;

use App\Filament\Resources\PlatformAutomationWorkflowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlatformAutomationWorkflow extends EditRecord
{
    protected static string $resource = PlatformAutomationWorkflowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
