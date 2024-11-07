<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplyIncidentResource\Pages;
use App\Filament\Resources\SupplyIncidentResource\RelationManagers;
use App\Models\Supply;
use App\Models\SupplyIncident;
use App\SupplyIncidents;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplyIncidentResource extends Resource
{
    protected static ?string $model = SupplyIncident::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Supply';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supply_id')
                    ->native(false)
                    ->label('Supply')
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
                    ->getSearchResultsUsing(fn(string $search): array => Supply::select('description', 'id')->whereAny(['description', 'id'], 'like', "%{$search}%")->limit(20)->get()->pluck('select_display', 'id')->toArray())
                    ->searchable()
                    ->live()
                    ->preload()
                    ->required(),

                Select::make('type')
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
                    ->options(SupplyIncidents::values())
                    ->required(),

                TextInput::make('quantity')
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
                    ->integer()
                    ->maxLength(7)
                    ->required()
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                    ])
                    ->hint(function (callable $get) {
                        $supplyId = $get('supply_id');
                        $supplyAvailable = Supply::find($supplyId)?->total;

                        return $supplyAvailable ? 'Available: ' . $supplyAvailable : '';
                    })
                    ->minValue(1)
                    ->maxValue(function (callable $get) {
                        $supplyId = $get('supply_id');
                        $supplyAvailable = Supply::find($supplyId)?->total;
                        return $supplyAvailable;
                    }),

                DatePicker::make('incident_date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->default(today())
                    ->beforeOrEqual('today')
                    ->required(),

                Textarea::make('remarks')
                    ->rules([
                        'string',
                        'regex:/[a-zA-Z]/',
                    ])
                    ->extraAttributes(['class' => 'resize-none'])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Incident Id'),
                TextColumn::make('supply.description')
                    ->label('Supply'),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('quantity'),
                TextColumn::make('incident_date')
                    ->date('F d, Y')
            ])
            ->filters([])
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
                TextEntry::make('id')
                    ->label('incident Id'),
                TextEntry::make('supply.description')
                    ->label('Supply'),
                TextEntry::make('type'),
                TextEntry::make('quantity')
                    ->label('Quantity'),
                TextEntry::make('incident_date')
                    ->date('F d, Y'),
                TextEntry::make('remarks'),
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
            'index' => Pages\ListSupplyIncidents::route('/'),
            'create' => Pages\CreateSupplyIncident::route('/create'),
            'edit' => Pages\EditSupplyIncident::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('supply')
            ->latest();
    }
}
