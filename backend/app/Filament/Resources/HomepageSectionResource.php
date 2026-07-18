<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageSectionResource\Pages;
use App\Models\HomepageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomepageSectionResource extends Resource
{
    protected static ?string $model = HomepageSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Site Builder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('layout_id')
                            ->relationship('layout', 'name')
                            ->required()
                            ->preload(),
                        Forms\Components\Select::make('section_key')
                            ->options([
                                'hero_slider' => 'Hero Slideshow Slider',
                                'bank_offers' => 'Bank Discount Coupon Slider',
                                'categories' => 'Shop By Category (Peach Borders Grid)',
                                'budget_bargains' => 'Budget Bargains Grid',
                                'featured_products' => 'Featured Products Catalog',
                                'trending_products' => 'Trending Products Feed',
                                'best_sellers' => 'Best Sellers List',
                                'new_arrivals' => 'New Arrivals Catalog',
                                'flash_sale' => 'Flash Sale Countdown Block',
                                'brand_carousel' => 'Brand Logotypes Carousel',
                                'lookbook' => 'Fashion Lookbook Banner',
                                'custom_html' => 'Custom HTML Block',
                                'newsletter' => 'Newsletter Subscription Box',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subtitle')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500),
                    ])->columns(2),

                Forms\Components\Section::make('Styling & Backgrounds')
                    ->schema([
                        Forms\Components\Select::make('background_type')
                            ->options([
                                'color' => 'Solid Background Color',
                                'image' => 'Background Image URL',
                                'video' => 'Background Video Loop URL',
                            ])
                            ->default('color'),
                        Forms\Components\TextInput::make('background_color')
                            ->placeholder('e.g. #ffffff or rgba(0,0,0,0)'),
                        Forms\Components\TextInput::make('background_image')
                            ->placeholder('Image URL path'),
                        Forms\Components\TextInput::make('background_video')
                            ->placeholder('Video URL path'),
                        Forms\Components\TextInput::make('padding')
                            ->default('py-12')
                            ->placeholder('e.g. py-12, py-16'),
                        Forms\Components\Select::make('width')
                            ->options([
                                'container' => 'Standard Box Container',
                                'full' => '100% Full Viewport Width',
                            ])
                            ->default('container'),
                    ])->columns(2),

                Forms\Components\Section::make('Actions & Custom Settings')
                    ->schema([
                        Forms\Components\TextInput::make('button_text')
                            ->placeholder('e.g. SHOP NOW'),
                        Forms\Components\TextInput::make('button_url')
                            ->placeholder('e.g. /catalog'),
                        Forms\Components\Select::make('layout_variation')
                            ->options([
                                'default' => 'Default Layout Style',
                                'grid' => 'Multi-column Grid',
                                'carousel' => 'Horizontal Carousel Slider',
                            ])
                            ->default('default'),
                        Forms\Components\KeyValue::make('settings')
                            ->label('Section JSON Parameters')
                            ->keyPlaceholder('Param Name')
                            ->valuePlaceholder('Value'),
                    ])->columns(2),

                Forms\Components\Section::make('Visibility & Scheduling')
                    ->schema([
                        Forms\Components\Toggle::make('is_enabled')
                            ->default(true)
                            ->label('Section Enabled'),
                        Forms\Components\Toggle::make('show_on_mobile')
                            ->default(true),
                        Forms\Components\Toggle::make('show_on_desktop')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Schedule Start Time'),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Schedule End Time'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('layout.name')
                    ->label('Layout Profile')
                    ->sortable(),
                Tables\Columns\TextColumn::make('section_key')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_enabled')
                    ->label('Enabled'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->label('Sort'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => Pages\ListHomepageSections::route('/'),
            'create' => Pages\CreateHomepageSection::route('/create'),
            'edit' => Pages\EditHomepageSection::route('/{record}/edit'),
        ];
    }
}
