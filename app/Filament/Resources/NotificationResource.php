<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use Filament\Forms\Form;
use Illuminate\Notifications\DatabaseNotification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class NotificationResource extends Resource
{
    protected static ?string $model = DatabaseNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('data.supply_name')
                    ->label('Supply')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data.message')
                    ->label('Message')
                    ->wrap(),
                Tables\Columns\TextColumn::make('data.type')
                    ->badge()
                    ->label('Type')
                    ->colors([
                        'danger' => 'low_stock',
                        'warning' => 'expiring',
                    ]),
                Tables\Columns\IconColumn::make('read_at')
                    ->label('Read')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'low_stock' => 'Low Stock',
                        'expiring' => 'Expiring Soon',
                    ])
                    ->attribute('data->type'),
                Tables\Filters\Filter::make('unread')
                    ->query(fn(Builder $query) => $query->whereNull('read_at')),
            ])
            ->actions([
                Tables\Actions\Action::make('view_supply')
                    ->icon('heroicon-o-eye')
                    ->url(
                        fn(DatabaseNotification $record) =>
                        route('filament.resources.supplies.edit', ['record' => $record->data['supply_id']])
                    ),
                Tables\Actions\Action::make('mark_as_read')
                    ->icon('heroicon-o-check')
                    ->hidden(fn(DatabaseNotification $record) => $record->read_at !== null)
                    ->action(function (DatabaseNotification $record) {
                        $record->markAsRead();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_as_read')
                        ->icon('heroicon-o-check')
                        ->action(fn(Collection $records) => $records->each->markAsRead()),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
