<?php

namespace App\Filament\Resources;

use App\Enum\SupplyReportAction;
use App\Filament\Resources\SupplyReportResource\Pages;
use App\Filament\Resources\SupplyReportResource\RelationManagers;
use App\Models\Supply;
use App\Models\SupplyReport;
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

class SupplyReportResource extends Resource
{
    protected static ?string $model = SupplyReport::class;

    protected static ?string $navigationGroup = 'Supply';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supply_id')
                    ->native(false)
                    ->relationship('supply')
                    ->label('Supply')
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
                    ->getSearchResultsUsing(fn(string $search): array => Supply::select('description', 'id')
                        ->whereAny(['description', 'id'], 'like', "%{$search}%")
                        ->limit(20)
                        ->get()
                        ->pluck('select_display', 'id')
                        ->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => Supply::find($value)?->select_display)
                    ->searchable()
                    ->live()
                    ->required(),

                Select::make('action')
                    ->options(SupplyReportAction::values())
                    ->native(false)
                    ->required(),

                TextInput::make('handler')
                    ->rules([
                        'string',
                        'regex:/^[a-zA-Z\s]+$/',
                    ])
                    ->maxLength(30)
                    ->required(),

                TextInput::make('quantity')
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
                    ->integer()
                    ->maxLength(7)
                    ->required()
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189 && event.keyCode !== 190 && event.keyCode !== 110)',
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
                        $action = $get('action');

                        return $action === SupplyReportAction::ADD->value ? null : $supplyAvailable;
                    }),

                Textarea::make('remarks')
                    ->rules([
                        'string',
                        'regex:/[a-zA-Z]/',
                    ])
                    ->extraAttributes(['class' => 'resize-none']),

                DatePicker::make('date_acquired')
                    ->label('Date')
                    ->closeOnDateSelection()
                    ->required()
                    ->default(today())
                    ->beforeOrEqual(function (string $operation, $record) {
                        if ($operation === 'edit') {
                            return $record->date_acquired->endOfDay();
                        }
                        return now()->endOfDay();
                    })
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('supply.description')
                    ->searchable(),

                TextColumn::make('quantity'),

                TextColumn::make('action'),

                TextColumn::make('date_acquired')
                    ->label('Date'),
            ])
            ->filters([
                //
            ])
            ->actions([
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

                TextEntry::make('id'),
                TextEntry::make('supply.description'),

                TextEntry::make('quantity'),

                TextEntry::make('quantity_returned'),

                TextEntry::make('unit_price'),

                TextEntry::make('date_acquired')
                    ->label('Date'),

                TextEntry::make('action')

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
            'index' => Pages\ListSupplyReports::route('/'),
            'create' => Pages\CreateSupplyReport::route('/create'),
            'edit' => Pages\EditSupplyReport::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('supply')
            ->latest();
    }
}
