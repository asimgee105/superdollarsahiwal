<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales & Orders Trends';

    protected function getData(): array
    {
        // Sample order analytics trends data
        return [
            'datasets' => [
                [
                    'label' => 'Total Orders Placed',
                    'data' => [15, 24, 35, 45, 60, 85, 120],
                    'backgroundColor' => '#ff3f6c',
                    'borderColor' => '#ff3f6c',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
