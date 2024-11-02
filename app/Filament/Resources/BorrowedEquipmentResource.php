<?php

namespace App\Filament\Resources;

use App\BorrowStatus;
use App\Enum\EquipmentStatus;
use App\Filament\Resources\BorrowedEquipmentResource\Pages;
use App\Models\BorrowedEquipment;
use App\Models\Equipment;
use App\Models\MissingEquipment;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class BorrowedEquipmentResource extends Resource
{
    protected static ?string $model = BorrowedEquipment::class;
    protected static ?string $navigationGroup = 'Equipment';
    protected static ?string $navigationLabel = 'Borrowed Equipment';
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('equipment_id')
                    ->native(false)
                    ->relationship('equipment', 'select_display')
                    ->label('Equipment')
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
                    ->getSearchResultsUsing(fn(string $search): array => Equipment::select('name', 'property_number', 'id')->whereAny(['name', 'property_number'], 'like', "%{$search}%")->limit(20)->get()->pluck('select_display', 'id')->toArray())
                    ->searchable()
                    ->reactive() // Make this field reactive
                    ->required(),

                TextInput::make('quantity')
                    ->integer()
                    ->maxLength(7)
                    ->required()
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                    ])
                    ->hint(function (callable $get, string $operation, $record) {
                        $equipmentId = $get('equipment_id');
                        $quantityAvailable = Equipment::find($equipmentId)?->quantity_available;
                        if ($operation === 'edit' && $record)
                            $quantityAvailable +=  $record->quantity;
                        return $quantityAvailable ? 'Available: ' . $quantityAvailable : '';
                    })
                    ->minValue(1)
                    ->maxValue(function (callable $get, string $operation, $record) {
                        $equipmentId = $get('equipment_id');
                        $quantityAvailable = Equipment::find($equipmentId)?->quantity_available;
                        if ($operation === 'edit' && $record)
                            $quantityAvailable +=  $record->quantity;
                        return  $quantityAvailable ?? 0;
                    }),


                TextInput::make('borrower_first_name')
                    ->rules([
                        'string',
                        'regex:/^[a-zA-Z\s]+$/',
                    ])
                    ->maxLength(30)
                    ->required(),

                TextInput::make('borrower_last_name')
                    ->rules([
                        'string',
                        'regex:/^[a-zA-Z\s]+$/',
                    ])
                    ->maxLength(30)
                    ->required(),

                TextInput::make('borrower_phone_number')
                    ->required()
                    ->numeric()
                    ->regex('/^09\d{9}$/')
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                    ])
                    ->maxLength(11),

                TextInput::make('borrower_email')
                    ->email()
                    ->required(),

                DatePicker::make('start_date')
                    ->native(false)
                    ->default(today())
                    ->closeOnDateSelection()
                    ->required(),

                DatePicker::make('end_date')
                    ->native(false)
                    ->after('start_date')
                    ->closeOnDateSelection()
                    ->required(),

                Select::make('status')
                    ->options(BorrowStatus::values())
                    ->hidden()
                    ->default(BorrowStatus::BORROWED->value),


                Hidden::make('status')
                    ->default(BorrowStatus::BORROWED->value)
                    ->dehydrated(true)
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('equipment.name')
                    ->label('Equipment')
                    ->searchable(['name']),

                TextColumn::make('quantity')
                    ->label('Quantity Borrowed'),

                TextColumn::make('equipment.quantity_available')
                    ->label('Quantity Available'),

                TextColumn::make('borrower_full_name')
                    ->searchable(['borrower_first_name', 'borrower_last_name']),

                TextColumn::make('start_date')
                    ->date('F d, Y'),

                TextColumn::make('end_date')
                    ->date('F d, Y'),

                TextColumn::make('status')
                    ->formatStateUsing(fn($state): string => Str::replace('_', ' ', Str::title(BorrowStatus::from($state)->name)))
                    ->badge()
                    ->color(fn(string $state): string => BorrowStatus::from($state)->getColor()),
                // TextColumn::make('is_returned')
                //     ->label('Is returned?')
                //     ->badge()
                //     ->color(fn($record) => $record === 'Yes' ? 'success' : 'warning'),


            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        BorrowStatus::BORROWED->value => 'Borrowed',
                        BorrowStatus::RETURNED->value => 'Returned',
                        BorrowStatus::PARTIALLY_MISSING->value => 'Partially Missing',
                        BorrowStatus::PARTIALLY_RETURNED->value => 'Partially Returned',
                        BorrowStatus::MISSING->value => 'Missing',
                        BorrowStatus::RETURNED_WITH_MISSING->value => 'Returned with Missing',
                        BorrowStatus::PARTIALLY_RETURNED_WITH_MISSING->value => 'Partially Returned with Missing',
                    ]),

                SelectFilter::make('equipment_id')
                    ->native(false)
                    ->label('Equipment')
                    ->relationship('equipment', 'name')
                    ->searchable()
                    ->getOptionLabelUsing(function ($value): ?string {
                        $equipment = Equipment::find($value);
                        return "$equipment->name (PN: $equipment->property_number)";
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('return')
                        ->color('primary')
                        ->form([
                            TextInput::make('quantity_returned')
                                ->integer()
                                ->extraInputAttributes([
                                    'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                                ])
                                ->label('Quantity to return')
                                ->maxValue(fn($record) => $record->quantity - ($record->total_quantity_returned + $record->total_quantity_missing))
                                ->hint(fn($record) => "Quantity in possession: " . $record->quantity - ($record->total_quantity_returned + $record->total_quantity_missing))
                                ->required(),
                        ])
                        ->action(function (array $data, BorrowedEquipment $borrowedEquipment) {
                            try {
                                $quantityReturned = $data['quantity_returned'];

                                $borrowedEquipment->status = $quantityReturned === $borrowedEquipment->quantity - ($borrowedEquipment->total_quantity_returned + $borrowedEquipment->total_quantity_missing) ? BorrowStatus::RETURNED->value : BorrowStatus::PARTIALLY_RETURNED->value;

                                $borrowedEquipment->total_quantity_returned += $quantityReturned;
                                $equipment = $borrowedEquipment->equipment;
                                DB::transaction(function () use ($borrowedEquipment, $equipment, $quantityReturned) {
                                    if ($borrowedEquipment->total_quantity_returned === $borrowedEquipment->quantity) {
                                        $borrowedEquipment->status = BorrowStatus::RETURNED->value;
                                        $borrowedEquipment->returned_date = date('Y-m-d');
                                    } else {
                                        $borrowedEquipment->status = self::getBorrowStatus($borrowedEquipment);
                                    }
                                    $totalAvailableQuantity = $equipment->quantity_available + $quantityReturned;
                                    $totalBorrowedQuantity = $equipment->quantity_borrowed - $quantityReturned;

                                    $equipment->quantity_available = $totalAvailableQuantity;
                                    $equipment->quantity_borrowed = $totalBorrowedQuantity;
                                    $equipment->status = self::getEquimentStatus($equipment);
                                    $equipment->save();
                                    $borrowedEquipment->save();
                                });
                                Notification::make()
                                    ->title('Success')
                                    ->body('Updated Successfully')
                                    ->success()
                                    ->send();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->success()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('report missing item')
                        ->color('danger')
                        ->form([
                            Section::make()
                                ->schema([
                                    TextInput::make('quantity_missing')
                                        ->integer()
                                        ->extraInputAttributes([
                                            'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                                        ])
                                        ->label('Quantity missing')
                                        ->maxValue(fn($record) => $record->quantity - ($record->total_quantity_returned + $record->total_quantity_missing))
                                        ->hint(fn($record) => "Quantity in possession: " . $record->quantity - ($record->total_quantity_returned + $record->total_quantity_missing))
                                        ->required(),

                                    TextInput::make('reported_by')
                                        ->rules([
                                            'string',
                                            'regex:/^[a-zA-Z\s]+$/',
                                        ])
                                        ->required(),

                                    Textarea::make('description')
                                        ->rules([
                                            'string',
                                            'regex:/[a-zA-Z]/',
                                        ])
                                        ->extraAttributes(['class' => 'resize-none'])
                                        ->columnSpan(2),


                                ])->columns(2)
                        ])->action(function (array $data, BorrowedEquipment $borrowedEquipment) {
                            $quantityMissing = $data['quantity_missing'];
                            try {
                                DB::transaction(function () use ($quantityMissing, $data, $borrowedEquipment) {
                                    // Create a missign equipmen report
                                    MissingEquipment::create([
                                        'borrowed_equipment_id' => $borrowedEquipment->id,
                                        'equipment_id' => $borrowedEquipment->equipment->id,
                                        'quantity' => $quantityMissing,
                                        'reported_by' => $data['reported_by'],
                                        'description' => $data['description'],
                                        'reported_date' => today()->format('Y-m-d'),
                                    ]);
                                    // Substract the missing equipment quantity to equipment borrowed quantity and add it on missing quantity
                                    $borrowedEquipment->total_quantity_missing += $quantityMissing;

                                    $borrowedEquipment->status = self::getBorrowStatus($borrowedEquipment);

                                    $equipment = $borrowedEquipment->equipment;
                                    $equipment->quantity_borrowed -= $quantityMissing;
                                    $equipment->quantity_missing += $quantityMissing;
                                    $equipment->status = self::getEquimentStatus($equipment);

                                    $equipment->save();
                                    $borrowedEquipment->save();
                                });
                                Notification::make()
                                    ->title('Success')
                                    ->success()
                                    ->send();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->success()
                                    ->send();
                            }
                        }),

                ])->visible(fn($record) => $record->status !== BorrowStatus::RETURNED->value && $record->status !== BorrowStatus::RETURNED_WITH_MISSING->value),


                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->color('danger'),
                Tables\Actions\RestoreAction::make()
                    ->requiresConfirmation()
                    ->color('warning'),
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
                \Filament\Infolists\Components\Section::make('Equipment Details')
                    ->schema([
                        TextEntry::make('equipment.name')
                            ->label('Name'),
                        TextEntry::make('equipment.property_number')
                            ->label('Property Number'),
                    ])->columns(2),

                \Filament\Infolists\Components\Section::make('Borrow Log Details')
                    ->schema([
                        TextEntry::make('quantity')
                            ->label('Quantity Borrowed'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('total_quantity_returned'),
                        TextEntry::make('total_quantity_missing'),
                        TextEntry::make('start_date')
                            ->date('F d, Y'),
                        TextEntry::make('end_date')
                            ->date('F d, Y'),
                    ])->columns(2),

                \Filament\Infolists\Components\Section::make('Borrower Details')
                    ->schema([
                        TextEntry::make('borrower_first_name'),

                        TextEntry::make('borrower_last_name'),

                        TextEntry::make('borrower_email'),

                        TextEntry::make('borrower_phone_number'),
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
            'index' => Pages\ListBorrowedEquipment::route('/'),
            'create' => Pages\CreateBorrowedEquipment::route('/create'),
            'edit' => Pages\EditBorrowedEquipment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('equipment')->latest();
    }

    public static function getEquimentStatus($equipment)
    {
        $totalAvailableQuantity = $equipment->quantity_available;
        $totalBorrowedQuantity = $equipment->quantity_borrowed;
        // Full borrowed is when total avaliable is 0 and qunatity borrowed is greater than 0]
        if ($totalAvailableQuantity === 0 && $totalBorrowedQuantity > 0)
            return EquipmentStatus::FULLY_BORROWED->value;
        // Partaially borrowed is when totalAvailable qunatity is not equals to 0 and borrowed is greater than 0
        if ($totalAvailableQuantity > 0 && $totalBorrowedQuantity > 0)
            return EquipmentStatus::PARTIALLY_BORROWED->value;
        if ($totalAvailableQuantity === 0 && $totalBorrowedQuantity === 0 && $equipment->condemned === $equipment->quantity)
            return EquipmentStatus::CONDEMNED->value;

        return EquipmentStatus::ACTIVE->value;
    }

    public static function getBorrowStatus($borrowedEquipment)
    {
        $totalMissingQuantity = $borrowedEquipment->total_quantity_missing;
        $borrowedQuantity = $borrowedEquipment->quantity;
        $totalReturnedQuantity = $borrowedEquipment->total_quantity_returned;

        // Fully missing
        if ($totalMissingQuantity === $borrowedQuantity) {
            return BorrowStatus::MISSING->value;
        }

        // Partially returned with missing items
        if ($totalMissingQuantity > 0 && $totalReturnedQuantity > 0 && $borrowedQuantity !== ($totalMissingQuantity + $totalReturnedQuantity)) {
            return BorrowStatus::PARTIALLY_RETURNED_WITH_MISSING->value;
        }

        // Returned with missing items (no partial returns)
        if ($totalMissingQuantity > 0 && $borrowedQuantity === ($totalMissingQuantity + $totalReturnedQuantity)) {
            return BorrowStatus::RETURNED_WITH_MISSING->value;
        }

        // Partially missing (no returns)
        if ($totalMissingQuantity > 0 && $totalReturnedQuantity === 0) {
            return BorrowStatus::PARTIALLY_MISSING->value;
        }

        // Fully returned with no missing items
        if ($totalMissingQuantity === 0 && $totalReturnedQuantity === $borrowedQuantity) {
            return BorrowStatus::RETURNED->value;
        }

        // Partially returned with no missing items
        if ($totalMissingQuantity === 0 && $totalReturnedQuantity < $borrowedQuantity) {
            return BorrowStatus::PARTIALLY_RETURNED->value;
        }

        // Default case: still borrowed
        return BorrowStatus::BORROWED->value;
    }
}
