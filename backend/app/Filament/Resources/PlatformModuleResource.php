<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformModuleResource\Pages;
use App\Models\PlatformModule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlatformModuleResource extends Resource
{
    protected static ?string $model = PlatformModule::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'Platform Core';

    protected static ?string $navigationLabel = 'Module Manager';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->disabled()
                            ->required(),
                        Forms\Components\TextInput::make('version')
                            ->disabled(),
                        Forms\Components\Toggle::make('is_enabled')
                            ->label('Active Feature Module')
                            ->default(true),
                        Forms\Components\Textarea::make('description')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('version'),
                Tables\Columns\ToggleColumn::make('is_enabled')->label('Active Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlatformModules::route('/'),
            'create' => Pages\CreatePlatformModule::route('/create'),
            'edit' => Pages\EditPlatformModule::route('/{record}/edit'),
        ];
    }
}
