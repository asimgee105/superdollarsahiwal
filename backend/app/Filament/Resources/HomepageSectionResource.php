<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageSectionResource\Pages;
use App\Models\HomepageSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomepageSectionResource extends Resource
{
    protected static ?string $model = HomepageSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Site Builder';

    protected static ?string $navigationLabel = 'Homepage Sections';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ── Basic Info ──────────────────────────────────────────────────
            Forms\Components\Section::make('Section Identity')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\Select::make('layout_id')
                        ->relationship('layout', 'name')
                        ->required()
                        ->preload()
                        ->columnSpan(1),

                    Forms\Components\Select::make('section_key')
                        ->label('Section Type')
                        ->options([
                            'hero_slider'       => '🎠 Hero Slideshow Slider',
                            'bank_offers'       => '🏦 Bank Discount Coupon Slider',
                            'categories'        => '🗂️ Shop By Category Grid',
                            'budget_bargains'   => '💰 Budget Bargains Grid',
                            'promo_banners'     => '📢 Promotional Banners',
                            'featured_products' => '⭐ Featured Products',
                            'trending_products' => '🔥 Trending Products',
                            'best_sellers'      => '🏆 Best Sellers',
                            'new_arrivals'      => '✨ New Arrivals',
                            'flash_sale'        => '⚡ Flash Sale Countdown',
                            'brand_carousel'    => '🏷️ Brand Logos Carousel',
                            'lookbook'          => '📸 Fashion Lookbook',
                            'newsletter'        => '📧 Newsletter Subscription',
                            'custom_html'       => '🧩 Custom HTML Block',
                        ])
                        ->required()
                        ->live()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('subtitle')
                        ->maxLength(255)
                        ->columnSpan(1),

                    Forms\Components\Textarea::make('description')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ── Styling ─────────────────────────────────────────────────────
            Forms\Components\Section::make('Styling & Background')
                ->icon('heroicon-o-paint-brush')
                ->collapsed()
                ->schema([
                    Forms\Components\Select::make('background_type')
                        ->options([
                            'color' => 'Solid Color',
                            'image' => 'Background Image',
                            'video' => 'Background Video',
                        ])
                        ->default('color')
                        ->live(),

                    Forms\Components\ColorPicker::make('background_color')
                        ->label('Background Color')
                        ->visible(fn (Get $get) => $get('background_type') === 'color'),

                    Forms\Components\TextInput::make('background_image')
                        ->label('Image URL')
                        ->placeholder('https://...')
                        ->visible(fn (Get $get) => $get('background_type') === 'image'),

                    Forms\Components\TextInput::make('background_video')
                        ->label('Video URL')
                        ->placeholder('https://...')
                        ->visible(fn (Get $get) => $get('background_type') === 'video'),

                    Forms\Components\TextInput::make('padding')
                        ->default('py-12')
                        ->placeholder('py-12')
                        ->helperText('Tailwind padding class e.g. py-8, py-16'),

                    Forms\Components\TextInput::make('margin')
                        ->placeholder('my-0')
                        ->helperText('Tailwind margin class e.g. my-4'),

                    Forms\Components\Select::make('width')
                        ->options([
                            'container' => 'Boxed Container (max-w-7xl)',
                            'full'      => 'Full Viewport Width',
                        ])
                        ->default('container'),

                    Forms\Components\Select::make('animation')
                        ->options([
                            'none'  => 'No Animation',
                            'fade'  => 'Fade In',
                            'slide' => 'Slide In',
                            'zoom'  => 'Zoom In',
                        ])
                        ->default('fade'),
                ])
                ->columns(2),

            // ── CTA ─────────────────────────────────────────────────────────
            Forms\Components\Section::make('Call To Action Button')
                ->icon('heroicon-o-cursor-arrow-rays')
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('button_text')
                        ->placeholder('SHOP NOW'),
                    Forms\Components\TextInput::make('button_url')
                        ->placeholder('/catalog/'),
                    Forms\Components\Select::make('layout_variation')
                        ->options([
                            'default'  => 'Default',
                            'grid'     => 'Grid Layout',
                            'carousel' => 'Carousel',
                            'slider'   => 'Slider',
                        ])
                        ->default('default'),
                ])
                ->columns(3),

            // ── Hero Slider Settings (shown only for hero_slider) ────────────
            Forms\Components\Section::make('🎠 Hero Slider Settings')
                ->icon('heroicon-o-squares-2x2')
                ->description('Control slider dimensions and manage individual slide designs.')
                ->visible(fn (Get $get) => $get('section_key') === 'hero_slider')
                ->schema([

                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\TextInput::make('settings.slider_height')
                            ->label('Slider Height')
                            ->default('420px')
                            ->placeholder('420px')
                            ->helperText('CSS height e.g. 420px, 500px, 60vh')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('settings.slider_width')
                            ->label('Slider Width')
                            ->default('100%')
                            ->placeholder('100%')
                            ->helperText('CSS width e.g. 100%, 1280px')
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('settings.autoplay')
                            ->label('Auto-play Slides')
                            ->default(true)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('settings.autoplay_delay')
                            ->label('Auto-play Delay (ms)')
                            ->numeric()
                            ->default(4500)
                            ->placeholder('4500')
                            ->helperText('Milliseconds between slides')
                            ->columnSpan(1),
                    ]),

                    Forms\Components\Repeater::make('settings.slides')
                        ->label('Slide Designs')
                        ->helperText('Add, remove, or reorder slides. Toggle each slide active/inactive independently.')
                        ->schema([

                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Slide Label (Admin Only)')
                                    ->placeholder('Design 1: Yellow Gradient')
                                    ->columnSpan(2),

                                Forms\Components\Toggle::make('active')
                                    ->label('Active (Show on site)')
                                    ->default(true)
                                    ->columnSpan(1),
                            ]),

                            Forms\Components\Select::make('design')
                                ->label('Design Template')
                                ->options([
                                    'classic_gradient' => '🟡 Classic Yellow Gradient',
                                    'rose_pink'        => '🌸 Rose Pink (Women)',
                                    'dark_night'       => '🌙 Dark Night (GenZ)',
                                    'mint_sports'      => '🌿 Mint Green (Sports)',
                                    'custom'           => '🎨 Custom (Manual BG)',
                                ])
                                ->default('classic_gradient')
                                ->live(),

                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('height')
                                    ->label('Slide Height Override')
                                    ->placeholder('420px')
                                    ->helperText('Leave empty to use global height'),

                                Forms\Components\TextInput::make('width')
                                    ->label('Slide Width Override')
                                    ->placeholder('100%')
                                    ->helperText('Leave empty to use global width'),

                                Forms\Components\TextInput::make('bg')
                                    ->label('Tailwind Gradient Classes')
                                    ->placeholder('from-[#fae04b] to-[#fae66d]')
                                    ->helperText('Used for "custom" design'),
                            ]),

                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Main Heading')
                                    ->placeholder('Men\'s Fashion'),

                                Forms\Components\TextInput::make('subtitle')
                                    ->label('Subtitle Text')
                                    ->placeholder('STARTING AT'),

                                Forms\Components\TextInput::make('price')
                                    ->label('Price / Offer Text')
                                    ->placeholder('Rs 999'),
                            ]),

                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('logoText')
                                    ->label('Background Logo Text')
                                    ->placeholder('AURA'),

                                Forms\Components\TextInput::make('btnLabel')
                                    ->label('Button Label')
                                    ->placeholder('SHOP NOW'),

                                Forms\Components\TextInput::make('btnUrl')
                                    ->label('Button URL')
                                    ->placeholder('/catalog/?category=men'),
                            ]),

                            Forms\Components\Repeater::make('images')
                                ->label('Slide Images (max 2)')
                                ->schema([
                                    Forms\Components\TextInput::make('url')
                                        ->label('Image URL')
                                        ->placeholder('https://images.unsplash.com/...')
                                        ->columnSpanFull(),
                                ])
                                ->maxItems(2)
                                ->defaultItems(2)
                                ->addActionLabel('Add Image'),
                        ])
                        ->addActionLabel('+ Add New Slide Design')
                        ->reorderable()
                        ->collapsible()
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['title'] ?? 'Slide')
                        ->columnSpanFull(),
                ]),

            // ── Generic JSON Settings (for other sections) ───────────────────
            Forms\Components\Section::make('Section Data (JSON)')
                ->icon('heroicon-o-code-bracket')
                ->description('Raw JSON settings for this section. Used by categories, bank_offers, budget_bargains, etc.')
                ->visible(fn (Get $get) => $get('section_key') !== 'hero_slider')
                ->collapsed()
                ->schema([
                    Forms\Components\Textarea::make('settings')
                        ->label('Settings JSON')
                        ->rows(14)
                        ->helperText('Valid JSON object. Changes reflect directly on the frontend. Example: {"categories":[{"title":"Men","image":"...","discount":"UP TO 60% OFF","url":"/catalog/?category=men"}]}')
                        ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                        ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                        ->columnSpanFull(),
                ]),

            // ── Visibility & Scheduling ──────────────────────────────────────
            Forms\Components\Section::make('Visibility & Schedule')
                ->icon('heroicon-o-eye')
                ->schema([
                    Forms\Components\Toggle::make('is_enabled')
                        ->label('Section Enabled')
                        ->default(true)
                        ->helperText('Disable to hide this section completely'),

                    Forms\Components\Toggle::make('show_on_mobile')
                        ->label('Show on Mobile')
                        ->default(true),

                    Forms\Components\Toggle::make('show_on_desktop')
                        ->label('Show on Desktop')
                        ->default(true),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower number = appears first'),

                    Forms\Components\DateTimePicker::make('start_date')
                        ->label('Schedule Start'),

                    Forms\Components\DateTimePicker::make('end_date')
                        ->label('Schedule End'),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('layout.name')
                    ->label('Layout')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('section_key')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match($state) {
                        'hero_slider'     => 'warning',
                        'categories'      => 'success',
                        'bank_offers'     => 'info',
                        'budget_bargains' => 'primary',
                        'promo_banners'   => 'danger',
                        default           => 'gray',
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('is_enabled')
                    ->label('Enabled'),

                Tables\Columns\ToggleColumn::make('show_on_mobile')
                    ->label('Mobile'),

                Tables\Columns\ToggleColumn::make('show_on_desktop')
                    ->label('Desktop'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('section_key')
                    ->label('Section Type')
                    ->options([
                        'hero_slider'     => 'Hero Slider',
                        'bank_offers'     => 'Bank Offers',
                        'categories'      => 'Categories',
                        'budget_bargains' => 'Budget Bargains',
                        'promo_banners'   => 'Promo Banners',
                    ]),
                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label('Enabled Status'),
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
            'index'  => Pages\ListHomepageSections::route('/'),
            'create' => Pages\CreateHomepageSection::route('/create'),
            'edit'   => Pages\EditHomepageSection::route('/{record}/edit'),
        ];
    }
}
