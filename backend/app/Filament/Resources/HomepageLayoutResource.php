<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageLayoutResource\Pages;
use App\Models\HomepageLayout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomepageLayoutResource extends Resource
{
    protected static ?string $model = HomepageLayout::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static ?string $navigationGroup = 'Site Builder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Toggle::make('is_active')
                            ->default(false)
                            ->label('Active Layout'),
                    ]),
                Forms\Components\Section::make('Layout Styles')
                    ->schema([
                        Forms\Components\Select::make('header_style')
                            ->options([
                                'classic' => 'Classic Header',
                                'minimal' => 'Minimalist Header',
                                'center' => 'Centered Logo Header',
                            ])
                            ->required(),
                        Forms\Components\Select::make('hero_style')
                            ->options([
                                'slider' => 'Campaign Slideshow',
                                'split' => 'Split Screen Bold Banner',
                                'video' => 'Cinematic Loop Video',
                            ])
                            ->required(),
                        Forms\Components\Select::make('category_style')
                            ->options([
                                'grid' => 'Peach Border Grid (6 Cols)',
                                'carousel' => 'Dynamic Carousel Slider',
                            ])
                            ->required(),
                        Forms\Components\Select::make('product_card_style')
                            ->options([
                                'card' => 'Classic Product Card',
                                'overlay' => 'Visual Overlay Hover Card',
                            ])
                            ->required(),
                        Forms\Components\Select::make('footer_style')
                            ->options([
                                'default' => 'Detailed Directory Footer (Myntra)',
                                'simple' => 'Simple Copyright Footer',
                            ])
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('header_style')
                    ->badge(),
                Tables\Columns\TextColumn::make('hero_style')
                    ->badge(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomepageLayouts::route('/'),
            'create' => Pages\CreateHomepageLayout::route('/create'),
            'edit' => Pages\EditHomepageLayout::route('/{record}/edit'),
        ];
    }
}
