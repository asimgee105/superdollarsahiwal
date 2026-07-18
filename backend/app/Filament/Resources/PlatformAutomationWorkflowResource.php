<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformAutomationWorkflowResource\Pages;
use App\Models\PlatformAutomationWorkflow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlatformAutomationWorkflowResource extends Resource
{
    protected static ?string $model = PlatformAutomationWorkflow::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Platform Core';

    protected static ?string $navigationLabel = 'Automation Workflows';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Select::make('trigger_event')
                            ->options([
                                'user_registered' => 'When a User Registers',
                                'order_created' => 'When an Order is Created',
                                'order_paid' => 'When an Order is Paid',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Workflow Actions Pipeline')
                    ->schema([
                        Forms\Components\Repeater::make('actions')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'email' => 'Send Customer Email',
                                        'sms' => 'Send SMS Mobile Alert',
                                        'webhook' => 'Trigger Outgoing Webhook URL',
                                    ])
                                    ->required(),
                                Forms\Components\Textarea::make('content')
                                    ->placeholder('Action configuration payload or template body'),
                            ])
                            ->columns(2)
                            ->label('Action items'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('trigger_event')->badge(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Active Status'),
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
            'index' => Pages\ListPlatformAutomationWorkflows::route('/'),
            'create' => Pages\CreatePlatformAutomationWorkflow::route('/create'),
            'edit' => Pages\EditPlatformAutomationWorkflow::route('/{record}/edit'),
        ];
    }
}
