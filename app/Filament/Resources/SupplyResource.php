<?php

namespace App\Filament\Resources;

use App\Enum\Unit;
use App\Filament\Resources\SupplyResource\Pages;
use App\Filament\Resources\SupplyResource\RelationManagers;
use App\Models\Category;
use App\Models\Supply;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;
    protected static ?string $navigationGroup = 'Supply';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Department Details')
                    ->schema([
                        TextInput::make('description')->required(),
                        Select::make('unit')
                            ->options(Unit::values())
                            ->native(false)
                            ->required(),

                        TextInput::make('quantity')
                            ->maxLength(30)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $set('total', $state ?? 0);
                            })
                            ->required(),

                        Hidden::make('total')->required(),

                        DatePicker::make('expiry_date')
                            ->native(false),

                        Select::make('categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required(),

                        Radio::make('is_consumable')
                            ->label('Is consumable?')
                            ->boolean()
                            ->inline()
                            ->required()
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->searchable(),

                TextColumn::make('quantity'),

                TextColumn::make('used'),

                TextColumn::make('total')
                    ->color(fn($record) => $record->total < 50 ? 'danger' : 'success'),

                TextColumn::make('categories.name')
                    ->badge(),

                TextColumn::make('expiry_date')
                    ->date('F d, Y'),
            ])
            ->filters([
                SelectFilter::make('categories')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->preload()
                    ->searchable()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('description'),
                TextEntry::make('unit'),
                TextEntry::make('quantity'),
                TextEntry::make('used'),
                TextEntry::make('recently_added'),
                TextEntry::make('total'),
                TextEntry::make('categories.name')
                    ->badge(),
                TextEntry::make('expiry_date')
                    ->date('F d, Y'),
                TextEntry::make(name: 'is_consumable')
                    ->formatStateUsing(fn($record) => $record ? 'Yes' : 'No'),
            ])->columns(2);
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
            'index' => Pages\ListSupplies::route('/'),
            'create' => Pages\CreateSupply::route('/create'),
            'edit' => Pages\EditSupply::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['categories'])->orderBy('total');
    }
}
