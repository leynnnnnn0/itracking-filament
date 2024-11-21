<?php

namespace App\Filament\Resources;

use App\Enum\SupplyIncidentStatus;
use App\Enum\SupplyReportAction;
use App\Filament\Resources\SupplyIncidentResource\Pages;
use App\Filament\Resources\SupplyIncidentResource\RelationManagers;
use App\Models\Supply;
use App\Models\SupplyIncident;
use App\Models\SupplyReport;
use App\SupplyIncidents;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class SupplyIncidentResource extends Resource
{
    protected static ?string $model = SupplyIncident::class;

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
                TextColumn::make('supply.id')
                    ->label('Supply Id'),
                TextColumn::make('supply.description')
                    ->label('Supply'),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('quantity'),
                TextColumn::make('status'),
                TextColumn::make('incident_date')
                    ->date('F d, Y')
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Archive')
                    ->label('Archive')
                    ->modalHeading('Archive Supply')
                    ->successNotificationTitle('Archived'),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('found')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Please confirm that the information provided is accurate before proceeding. This action will update the supply details record accordingly.')
                        ->modalSubmitActionLabel('Submit')
                        ->form([
                            TextInput::make('quantity')
                                ->integer()
                                ->maxLength(10)
                                ->hint(fn($record) => "Available: " . $record->quantity - $record->reconciled_quantity)
                                ->minValue(1)
                                ->maxValue(fn($record) => $record->quantity - $record->reconciled_quantity)
                                ->live()
                                ->extraInputAttributes([
                                    'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189 && event.keyCode !== 190 && event.keyCode !== 110)',
                                ])
                                ->required(),

                            TextInput::make('handler')
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
                                    $record->reconciled_quantity += $data['quantity'];

                                    $supply = $record->supply;

                                    $supply->missing -= $data['quantity'];
                                    $supply->total += $data['quantity'];

                                    SupplyReport::create([
                                        'supply_id' => $supply->id,
                                        'handler' => $data['handler'],
                                        'quantity' => $data['quantity'],
                                        'quantity_returned' => 0,
                                        'remarks' => $data['remarks'],
                                        'date_acquired' => $data['date_acquired'],
                                        'action' => SupplyReportAction::FOUND->value
                                    ]);

                                    $record->save();
                                    $supply->save();
                                });
                                Notification::make()
                                    ->title('Updated Successfully.')
                                    ->success()
                                    ->send();
                            } catch (Exception $e) {
                                dd($e);
                                Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                ])->visible(fn($record) => $record->type === 'missing' && $record->status = 'active' && $record->quantity !== $record->reconciled_quantity)

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Archive')
                        ->modalHeading('Archive Supply Incidents')
                        ->successNotificationTitle('Archived'),
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
                TextEntry::make('reconciled_quantity'),
                TextEntry::make('incident_date')
                    ->date('F d, Y'),
                TextEntry::make('status'),
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
