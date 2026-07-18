<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'OMS & CRM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'flat' => 'Flat Discount (Rs.)',
                                'percentage' => 'Percentage Discount (%)',
                                'free_shipping' => 'Free Shipping Coupon',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('value')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('min_cart_value')
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('max_discount')
                            ->numeric()
                            ->placeholder('e.g. Max Rs. 500 discount'),
                        Forms\Components\TextInput::make('usage_limit')
                            ->numeric()
                            ->placeholder('Global total usage limit'),
                        Forms\Components\TextInput::make('usage_per_user')
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('priority')
                            ->numeric()
                            ->default(0),
                        Forms\Components\DateTimePicker::make('starts_at'),
                        Forms\Components\DateTimePicker::make('expires_at'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Coupon Applicability Filters')
                    ->schema([
                        Forms\Components\Select::make('applicable_categories')
                            ->options(fn () => Category::pluck('name', 'id')->toArray())
                            ->multiple()
                            ->preload()
                            ->label('Limit to specific categories'),
                        Forms\Components\Select::make('applicable_brands')
                            ->options(fn () => Brand::pluck('name', 'id')->toArray())
                            ->multiple()
                            ->preload()
                            ->label('Limit to specific brands'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('value')->sortable(),
                Tables\Columns\TextColumn::make('min_cart_value')->label('Min Order'),
                Tables\Columns\TextColumn::make('used_count')->label('Used count'),
                Tables\Columns\ToggleColumn::make('is_active'),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
