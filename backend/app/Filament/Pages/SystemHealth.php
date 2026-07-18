<?php

namespace App\Filament\Pages;

use App\Services\HealthMonitorService;
use Filament\Pages\Page;

class SystemHealth extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $title = 'System Health Metrics';

    protected static ?string $navigationGroup = 'Platform Core';

    protected static string $view = 'filament.pages.system-health';

    public array $metrics = [];

    public function mount(HealthMonitorService $monitorService): void
    {
        $this->metrics = $monitorService->getHealthMetrics();
    }
}
