<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Services\AiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Product Catalog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Product Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Details')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('barcode')
                                    ->maxLength(255),
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'simple' => 'Simple Product',
                                        'variable' => 'Variable Product',
                                        'grouped' => 'Grouped Product',
                                        'bundle' => 'Bundle Product',
                                        'digital' => 'Digital Product',
                                        'gift_card' => 'Gift Card Product',
                                        'subscription' => 'Subscription Product',
                                    ])
                                    ->default('simple')
                                    ->required(),
                                Forms\Components\Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->preload()
                                    ->searchable(),
                                Forms\Components\Select::make('label_id')
                                    ->relationship('label', 'name')
                                    ->label('Product Badge/Label')
                                    ->preload()
                                    ->searchable(),
                                Forms\Components\Textarea::make('short_description')
                                    ->maxLength(255)
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('generate_ai_short')
                                            ->label('AI Write')
                                            ->icon('heroicon-m-sparkles')
                                            ->action(function (Forms\Set $set, Forms\Get $get) {
                                                $name = $get('name');
                                                if ($name) {
                                                    $set('short_description', 'Discover premium design details on '.$name.'.');
                                                }
                                            })
                                    )
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(2000)
                                    ->hintAction(
                                        Forms\Components\Actions\Action::make('generate_ai_desc')
                                            ->label('AI Write Description')
                                            ->icon('heroicon-m-sparkles')
                                            ->action(function (Forms\Set $set, Forms\Get $get) {
                                                $name = $get('name');
                                                if ($name) {
                                                    $ai = new AiService;
                                                    $set('description', $ai->generateDescription($name));
                                                    $set('meta_title', $name.' | Shop Online');
                                                    $set('meta_description', $ai->generateMeta($name));
                                                }
                                            })
                                    )
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Classifications')
                            ->schema([
                                Forms\Components\Select::make('categories')
                                    ->relationship('categories', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('collections')
                                    ->relationship('collections', 'name')
                                    ->multiple()
                                    ->preload(),
                                Forms\Components\KeyValue::make('highlights')
                                    ->label('Product Highlights Points')
                                    ->keyPlaceholder('Highlight Tag')
                                    ->valuePlaceholder('Detail text'),
                                Forms\Components\KeyValue::make('specifications')
                                    ->label('Technical Specifications')
                                    ->keyPlaceholder('Property (e.g. Sleeve)')
                                    ->valuePlaceholder('Value (e.g. Full Sleeve)'),
                                Forms\Components\TextInput::make('origin_country')
                                    ->placeholder('e.g. India, Pakistan'),
                                Forms\Components\TextInput::make('wash_care')
                                    ->placeholder('e.g. Machine Wash Cold'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Media Gallery')
                            ->schema([
                                Forms\Components\Repeater::make('media')
                                    ->relationship('media')
                                    ->schema([
                                        Forms\Components\TextInput::make('path')
                                            ->required()
                                            ->label('Image/Video URL Path')
                                            ->placeholder('e.g. https://images.unsplash.com/photo-...'),
                                        Forms\Components\Select::make('type')
                                            ->options([
                                                'image' => 'Image Asset',
                                                'video' => 'Video Loop',
                                                '360' => '360 Image Angle',
                                            ])
                                            ->default('image')
                                            ->required(),
                                        Forms\Components\TextInput::make('alt_text')
                                            ->placeholder('Alternative Text'),
                                        Forms\Components\TextInput::make('sort_order')
                                            ->numeric()
                                            ->default(0),
                                    ])
                                    ->columns(4)
                                    ->orderable('sort_order'),
                            ])->columns(1),

                        Forms\Components\Tabs\Tab::make('Variant Options matrix')
                            ->schema([
                                Forms\Components\Repeater::make('variants')
                                    ->relationship('variants')
                                    ->schema([
                                        Forms\Components\TextInput::make('sku')
                                            ->label('Variant SKU')
                                            ->required(),
                                        Forms\Components\Select::make('size_id')
                                            ->relationship('size', 'name')
                                            ->preload()
                                            ->searchable(),
                                        Forms\Components\Select::make('color_id')
                                            ->relationship('color', 'name')
                                            ->preload()
                                            ->searchable(),
                                        Forms\Components\TextInput::make('price')
                                            ->numeric()
                                            ->required(),
                                        Forms\Components\TextInput::make('sale_price')
                                            ->numeric(),
                                        Forms\Components\KeyValue::make('attributes')
                                            ->label('Custom Spec Attributes')
                                            ->keyPlaceholder('Property')
                                            ->valuePlaceholder('Value'),
                                    ])
                                    ->columns(3)
                                    ->label('Variants list'),
                            ])->columns(1),

                        Forms\Components\Tabs\Tab::make('Relationships')
                            ->schema([
                                Forms\Components\Repeater::make('upsells_list')
                                    ->schema([
                                        Forms\Components\Select::make('related_id')
                                            ->relationship('upsells', 'title')
                                            ->label('Select Upsell Product')
                                            ->preload()
                                            ->required(),
                                    ])
                                    ->columns(1)
                                    ->label('Upsells'),
                            ]),

                        Forms\Components\Tabs\Tab::make('SEO Management')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title'),
                                Forms\Components\Textarea::make('meta_description'),
                                Forms\Components\TextInput::make('canonical_url'),
                            ])->columns(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('sku')->badge()->searchable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('brand.name')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\ToggleColumn::make('is_featured'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
