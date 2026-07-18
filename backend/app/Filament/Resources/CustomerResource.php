<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'OMS & CRM';

    protected static ?string $navigationLabel = 'Customers CRM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->label('User Roles (Group)'),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Addresses Book')
                    ->schema([
                        Forms\Components\Repeater::make('addresses')
                            ->relationship('addresses')
                            ->schema([
                                Forms\Components\TextInput::make('type')->required(),
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('phone')->required(),
                                Forms\Components\TextInput::make('address_line_1')->required(),
                                Forms\Components\TextInput::make('city')->required(),
                                Forms\Components\TextInput::make('state')->required(),
                                Forms\Components\TextInput::make('postal_code')->required(),
                            ])
                            ->columns(3)
                            ->disabled()
                            ->label('Addresses List'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')->badge()->label('User Level'),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
