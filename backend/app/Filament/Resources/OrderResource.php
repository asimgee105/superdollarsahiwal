<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'OMS & CRM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Order Manager')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Order Summary')
                            ->schema([
                                Forms\Components\TextInput::make('order_number')
                                    ->disabled()
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending Confirmation',
                                        'confirmed' => 'Confirmed',
                                        'processing' => 'Processing Details',
                                        'packed' => 'Items Packed',
                                        'shipped' => 'Shipped out',
                                        'delivered' => 'Delivered Successfully',
                                        'cancelled' => 'Cancelled',
                                        'returned' => 'Returned',
                                        'refunded' => 'Refunded',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->disabled(),
                                Forms\Components\Select::make('payment_method')
                                    ->options([
                                        'cod' => 'Cash On Delivery',
                                        'stripe' => 'Stripe Checkout',
                                        'paypal' => 'PayPal Express',
                                        'razorpay' => 'Razorpay Gateway',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('payment_status')
                                    ->options([
                                        'pending' => 'Pending Payment',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                        'refunded' => 'Refunded',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('total')
                                    ->numeric()
                                    ->disabled(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Shipping Address')
                            ->schema([
                                Forms\Components\TextInput::make('shipping_name')->required(),
                                Forms\Components\TextInput::make('shipping_phone')->required(),
                                Forms\Components\TextInput::make('shipping_address_line_1')->required(),
                                Forms\Components\TextInput::make('shipping_address_line_2'),
                                Forms\Components\TextInput::make('shipping_city')->required(),
                                Forms\Components\TextInput::make('shipping_state')->required(),
                                Forms\Components\TextInput::make('shipping_postal_code')->required(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Purchased Items')
                            ->schema([
                                Forms\Components\Repeater::make('items')
                                    ->relationship('items')
                                    ->schema([
                                        Forms\Components\TextInput::make('sku')
                                            ->label('SKU')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->disabled(),
                                        Forms\Components\TextInput::make('price')
                                            ->numeric()
                                            ->disabled(),
                                        Forms\Components\TextInput::make('total')
                                            ->numeric()
                                            ->disabled(),
                                    ])
                                    ->columns(4)
                                    ->disabled()
                                    ->label('Items List'),
                            ])->columns(1),

                        Forms\Components\Tabs\Tab::make('Activity Timeline')
                            ->schema([
                                Forms\Components\Repeater::make('timeline')
                                    ->relationship('timeline')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->required(),
                                        Forms\Components\TextInput::make('status')
                                            ->required(),
                                        Forms\Components\Textarea::make('description'),
                                    ])
                                    ->columns(2)
                                    ->label('Timeline Entries'),
                            ])->columns(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->placeholder('Guest User')->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'packed' => 'Packed',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'returned' => 'Returned',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Columns\TextColumn::make('total')->sortable(),
                Tables\Columns\SelectColumn::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
