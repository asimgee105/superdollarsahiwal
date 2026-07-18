<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\ReturnRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $revenue = Order::where('payment_status', 'paid')->sum('total');
        $ordersCount = Order::count();
        $pendingReturns = ReturnRequest::where('status', 'pending')->count();

        // Count variants with aggregate stock < 5
        $lowStockCount = ProductVariant::whereHas('inventoryItems', function ($q) {
            $q->whereColumn('quantity', '<', 'low_stock_threshold');
        })->count();

        return [
            Stat::make('Total Revenue', 'Rs. '.number_format($revenue, 2))
                ->description('Total Paid Orders Sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Total Orders', $ordersCount)
                ->description('Orders placed to date')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            Stat::make('Pending Returns', $pendingReturns)
                ->description('Needs admin review')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),
            Stat::make('Low Stock Alerts', $lowStockCount)
                ->description('Items with low inventory levels')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
