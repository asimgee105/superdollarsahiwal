<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductReviewResource\Pages;
use App\Models\ProductReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductReviewResource extends Resource
{
    protected static ?string $model = ProductReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Product Catalog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'title')
                            ->required()
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('rating')
                            ->options([
                                1 => '1 Star',
                                2 => '2 Stars',
                                3 => '3 Stars',
                                4 => '4 Stars',
                                5 => '5 Stars',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Approval',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_verified')
                            ->default(false)
                            ->label('Verified Purchase'),
                        Forms\Components\Textarea::make('comment')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Admin Moderation Replies')
                    ->schema([
                        Forms\Components\Repeater::make('replies')
                            ->relationship('replies')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->required(),
                                Forms\Components\Textarea::make('reply')
                                    ->required()
                                    ->maxLength(500),
                            ])
                            ->columns(2)
                            ->label('Replies List'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->placeholder('Guest User')->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->color(fn ($state) => $state >= 4 ? 'success' : ($state == 3 ? 'warning' : 'danger')),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Columns\ToggleColumn::make('is_verified')->label('Verified'),
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
            'index' => Pages\ListProductReviews::route('/'),
            'create' => Pages\CreateProductReview::route('/create'),
            'edit' => Pages\EditProductReview::route('/{record}/edit'),
        ];
    }
}
