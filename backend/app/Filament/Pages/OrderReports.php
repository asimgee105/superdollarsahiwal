<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $title = 'Sales Reports Manager';

    protected static ?string $navigationGroup = 'OMS & CRM';

    protected static string $view = 'filament.pages.order-reports';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
            'report_type' => 'sales',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('report_type')
                            ->options([
                                'sales' => 'Sales Order Ledger',
                                'revenue' => 'Revenue & Payment Summary',
                                'returns' => 'Returns & Refund Requests Log',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required(),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function exportCsv(): StreamedResponse
    {
        $input = $this->form->getState();
        $start = $input['start_date'].' 00:00:00';
        $end = $input['end_date'].' 23:59:59';
        $type = $input['report_type'];

        $orders = Order::whereBetween('created_at', [$start, $end])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="aura-report-'.$type.'-'.date('Y-m-d').'.csv"',
        ];

        return new StreamedResponse(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, ['Order Number', 'Customer Name', 'Status', 'Total', 'Payment Status', 'Date']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->shipping_name,
                    $order->status,
                    $order->total,
                    $order->payment_status,
                    $order->created_at->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
