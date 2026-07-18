<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActivityLogWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Executor')
                    ->placeholder('System/Guest'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Action Performed'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime(),
            ]);
    }
}
