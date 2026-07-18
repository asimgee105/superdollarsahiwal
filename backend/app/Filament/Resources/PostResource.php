<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'CMS & Blog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Article Manager')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Article Content')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Forms\Components\Textarea::make('short_description')
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('body')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('image_url')
                                    ->image()
                                    ->directory('blog')
                                    ->label('Featured Cover Image'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Publishing & SEO')
                            ->schema([
                                Forms\Components\Select::make('author_id')
                                    ->relationship('author', 'name')
                                    ->required(),
                                Forms\Components\Select::make('categories')
                                    ->relationship('categories', 'name')
                                    ->multiple()
                                    ->preload(),
                                Forms\Components\TextInput::make('reading_time')
                                    ->numeric()
                                    ->default(5)
                                    ->suffix('minutes'),
                                Forms\Components\DateTimePicker::make('published_at'),
                                Forms\Components\Toggle::make('is_published')
                                    ->default(false),

                                Forms\Components\Section::make('Search Engine Meta Settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title'),
                                        Forms\Components\Textarea::make('meta_description'),
                                    ])->columns(1),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')->label('Cover'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('author.name')->label('Author'),
                Tables\Columns\ToggleColumn::make('is_published')->label('Published'),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
