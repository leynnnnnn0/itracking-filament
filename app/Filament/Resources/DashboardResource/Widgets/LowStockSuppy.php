<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Supply;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockSuppy extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Supply::query()
                    ->where('total', '<', 10)
                    ->orderBy('total', 'asc')
            )
            ->columns([
                TextColumn::make('description')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unit')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->sortable(),

                TextColumn::make('total')
                    ->sortable()
                    ->color('danger'),

                TextColumn::make('categories.name')
                    ->badge()
                    ->separator(','),

                TextColumn::make('expiry_date')
                    ->date('F d, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(5);
    }
}
