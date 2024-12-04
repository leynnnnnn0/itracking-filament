<?php

namespace App\Filament\Resources;

use App\Enum\SupplyReportAction;
use App\Filament\Resources\SupplyReportResource\Pages;
use App\Filament\Resources\SupplyReportResource\RelationManagers;
use App\Models\Supply;
use App\Models\SupplyReport;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
                    ->options(SupplyReportAction::values(['return', 'found']))
                    ->native(false)
                    ->required(),

                TextInput::make('handler')
                    ->label('Person In-Charge')
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
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
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
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

                TextColumn::make('action')
                    ->badge()
                    ->formatStateUsing(fn($state) => Str::headline($state))
                    ->color(fn(string $state): string => SupplyReportAction::from($state)->getColor()),

                TextColumn::make('date_acquired')
                    ->label('Date')
                    ->date('F d, Y'),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
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
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('return')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->form([
                            TextInput::make('quantity')
                                ->integer()
                                ->maxLength(10)
                                ->hint(fn($record) => "Available: " . $record->quantity - $record->quantity_returned)
                                ->minValue(1)
                                ->maxValue(fn($record) => $record->quantity - $record->quantity_returned)
                                ->live()
                                ->extraInputAttributes([
                                    'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189 && event.keyCode !== 190 && event.keyCode !== 110)',
                                ])
                                ->required(),

                            TextInput::make('handler')
                                ->label("Person In-Charge")
                                ->rules([
                                    'string',
                                    'regex:/^[a-zA-Z\s]+$/',
                                ])
                                ->maxLength(30)
                                ->required(),

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
                        ])
                        ->action(function ($record, $data) {
                            try {
                                DB::transaction(function () use ($record, $data) {
                                    $record->quantity_returned += $data['quantity'];
                                    $supply = $record->supply;

                                    $supply->used -= $data['quantity'];
                                    $supply->total += $data['quantity'];

                                    SupplyReport::create([
                                        'supply_id' => $supply->id,
                                        'handler' => $data['handler'],
                                        'quantity' => $data['quantity'],
                                        'quantity_returned' => $data['quantity'],
                                        'remarks' => $data['remarks'],
                                        'date_acquired' => $data['date_acquired'],
                                        'action' => SupplyReportAction::RETURN->value
                                    ]);

                                    $record->save();
                                    $supply->save();
                                });

                                Notification::make()
                                    ->title("$record->description (ID: $record->id)")
                                    ->body('Details Updated')
                                    ->success()
                                    ->send();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->visible(fn($record) => $record->action === SupplyReportAction::DISPENSE->value && $record->quantity > $record->quantity_returned)
                ]),
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

                Section::make('Supply Information')
                    ->schema([
                        TextEntry::make('supply.id')
                            ->label('Supply Id'),

                        TextEntry::make('supply.description'),

                        TextEntry::make('supply.categories.name')
                            ->label('Categories')
                            ->badge(),



                    ])->columns(2),

                Section::make('Report Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Supply Report Id'),

                        TextEntry::make('action')
                            ->badge()
                            ->formatStateUsing(fn($state) => Str::headline($state))
                            ->color(fn(string $state): string => SupplyReportAction::from($state)->getColor()),

                        TextEntry::make('quantity'),

                        TextEntry::make('handler')
                            ->label("Person In-Charge"),

                        TextEntry::make('quantity_returned')
                            ->visible(fn($record) => $record->action === SupplyReportAction::DISPENSE->value),

                        TextEntry::make('date_acquired')
                            ->label('Date')
                            ->date('F d, Y'),

                        TextEntry::make('remarks'),


                    ])->columns(2)

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
