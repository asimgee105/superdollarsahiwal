<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformThemeResource\Pages;
use App\Models\PlatformTheme;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlatformThemeResource extends Resource
{
    protected static ?string $model = PlatformTheme::class;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationGroup = 'Platform Core';

    protected static ?string $navigationLabel = 'Theme Manager';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\KeyValue::make('variables')
                            ->keyPlaceholder('Style Variable (e.g. primary_color)')
                            ->valuePlaceholder('Value (e.g. #ff3f6c)'),
                        Forms\Components\Textarea::make('custom_css')
                            ->label('Override custom CSS styles')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('custom_js')
                            ->label('Override custom JS scripts')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Active Theme'),
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
            'index' => Pages\ListPlatformThemes::route('/'),
            'create' => Pages\CreatePlatformTheme::route('/create'),
            'edit' => Pages\EditPlatformTheme::route('/{record}/edit'),
        ];
    }
}
