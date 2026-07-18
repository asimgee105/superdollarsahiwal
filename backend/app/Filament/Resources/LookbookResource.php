<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LookbookResource\Pages;
use App\Models\Lookbook;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LookbookResource extends Resource
{
    protected static ?string $model = Lookbook::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'CMS & Blog';

    protected static ?string $navigationLabel = 'Lookbooks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\FileUpload::make('image_url')
                            ->image()
                            ->directory('lookbooks')
                            ->required()
                            ->label('Lookbook Photo'),
                        Forms\Components\Select::make('tagged_product_ids')
                            ->options(fn () => Product::pluck('title', 'id')->toArray())
                            ->multiple()
                            ->preload()
                            ->label('Tag products in this look'),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')->label('Look Image'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
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
            'index' => Pages\ListLookbooks::route('/'),
            'create' => Pages\CreateLookbook::route('/create'),
            'edit' => Pages\EditLookbook::route('/{record}/edit'),
        ];
    }
}
