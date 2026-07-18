<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturnRequestResource\Pages;
use App\Models\ReturnRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReturnRequestResource extends Resource
{
    protected static ?string $model = ReturnRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'OMS & CRM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'order_number')
                            ->disabled()
                            ->required(),
                        Forms\Components\Select::make('order_item_id')
                            ->relationship('orderItem', 'sku')
                            ->disabled()
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'return' => 'Return for Refund',
                                'exchange' => 'Exchange Size/Color',
                                'replacement' => 'Replacement (Defect)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('reason')
                            ->disabled()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'completed' => 'Completed / Refunded',
                            ])
                            ->required(),
                        Forms\Components\Select::make('pickup_status')
                            ->options([
                                'pending' => 'Pending Pickup',
                                'assigned' => 'Courier Assigned',
                                'picked' => 'Items Picked Up',
                                'details' => 'Received at Warehouse',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('refund_amount')
                            ->numeric()
                            ->required(),
                        Forms\Components\Textarea::make('customer_notes')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('orderItem.sku')->label('SKU'),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('reason'),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ]),
                Tables\Columns\SelectColumn::make('pickup_status')
                    ->options([
                        'pending' => 'Pending Pickup',
                        'assigned' => 'Assigned',
                        'picked' => 'Picked',
                        'details' => 'Received',
                    ]),
                Tables\Columns\TextColumn::make('refund_amount')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReturnRequests::route('/'),
            'create' => Pages\CreateReturnRequest::route('/create'),
            'edit' => Pages\EditReturnRequest::route('/{record}/edit'),
        ];
    }
}
