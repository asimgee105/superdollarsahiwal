<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformWebhookResource\Pages;
use App\Models\PlatformWebhook;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlatformWebhookResource extends Resource
{
    protected static ?string $model = PlatformWebhook::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Platform Core';

    protected static ?string $navigationLabel = 'Webhook Manager';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('url')
                            ->url()
                            ->required(),
                        Forms\Components\Select::make('event')
                            ->options([
                                'order.created' => 'Order Created Event',
                                'order.paid' => 'Order Paid Event',
                                'user.registered' => 'New Customer Registration',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('secret')
                            ->placeholder('HMAC signature secret key')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('url')->searchable(),
                Tables\Columns\TextColumn::make('event')->badge(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Active'),
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
            'index' => Pages\ListPlatformWebhooks::route('/'),
            'create' => Pages\CreatePlatformWebhook::route('/create'),
            'edit' => Pages\EditPlatformWebhook::route('/{record}/edit'),
        ];
    }
}
