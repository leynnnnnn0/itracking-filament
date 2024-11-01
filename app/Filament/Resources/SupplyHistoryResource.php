<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplyHistoryResource\Pages;
use App\Filament\Resources\SupplyHistoryResource\RelationManagers;
use App\Models\Supply;
use App\Models\SupplyHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplyHistoryResource extends Resource
{
    protected static ?string $model = SupplyHistory::class;
    protected static ?string $navigationGroup = 'Supply';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supply.description')
                    ->searchable(),

                TextColumn::make('quantity'),

                TextColumn::make('used'),

                TextColumn::make('added'),

                TextColumn::make('total'),

                TextColumn::make('created_at')
                    ->date('F d, Y'),

            ])
            ->filters([
                SelectFilter::make('categories')
                    ->multiple()
                    ->relationship('supply.categories', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('supply')
                    ->relationship('supply', 'description')
                    ->searchable(),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupplyHistories::route('/'),
            'create' => Pages\CreateSupplyHistory::route('/create'),
            'edit' => Pages\EditSupplyHistory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['supply'])->latest();
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
