<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Site Builder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\FileUpload::make('path')
                            ->required()
                            ->disk('public')
                            ->directory('uploads')
                            ->preserveFilenames()
                            ->storeFileNamesIn('filename')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->label('Upload File / Image'),
                        Forms\Components\TextInput::make('filename')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(true),
                        Forms\Components\TextInput::make('folder')
                            ->placeholder('e.g. banners, products')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Asset SEO Metadata')
                    ->schema([
                        Forms\Components\TextInput::make('alt_text')
                            ->placeholder('Alternative description text for search engines')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('title')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->disk('public')
                    ->circular()
                    ->label('Thumbnail'),
                Tables\Columns\TextColumn::make('filename')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('folder')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alt_text')
                    ->searchable(),
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
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
