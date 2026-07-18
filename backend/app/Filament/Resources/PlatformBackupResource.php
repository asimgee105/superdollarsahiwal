<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformBackupResource\Pages;
use App\Models\PlatformBackup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlatformBackupResource extends Resource
{
    protected static ?string $model = PlatformBackup::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Platform Core';

    protected static ?string $navigationLabel = 'Backup Manager';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('filename')
                            ->required(),
                        Forms\Components\TextInput::make('disk')
                            ->required(),
                        Forms\Components\TextInput::make('size_bytes')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('status')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('filename')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('disk'),
                Tables\Columns\TextColumn::make('size_bytes')->label('Size (Bytes)'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (PlatformBackup $record) => route('backup.download', $record->id))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListPlatformBackups::route('/'),
            'create' => Pages\CreatePlatformBackup::route('/create'),
            'edit' => Pages\EditPlatformBackup::route('/{record}/edit'),
        ];
    }
}
