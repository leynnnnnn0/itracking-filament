<?php

namespace App\Filament\Resources;

use App\BorrowStatus;
use App\Enum\EquipmentStatus;
use App\Filament\Resources\BorrowedEquipmentResource\Pages;
use App\Filament\Resources\BorrowedEquipmentResource\RelationManagers;
use App\Models\BorrowedEquipment;
use App\Models\Equipment;
use Carbon\Carbon;
use Exception;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BorrowedEquipmentResource extends Resource
{
    protected static ?string $model = BorrowedEquipment::class;
    protected static ?string $navigationGroup = 'Equipment';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('equipment_id')
                    ->native(false)
                    ->relationship('equipment')
                    ->label('Equipment')
                    ->getSearchResultsUsing(fn(string $search): array => Equipment::whereAny(['name', 'property_number'], 'like', "%{$search}%")->limit(20)->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->getOptionLabelUsing(function ($value): ?string {
                        $equipment = Equipment::find($value);
                        return "$equipment->name (PN: $equipment->property_number)";
                    })
                    ->required(),

                TextInput::make('quantity')
                    ->integer()
                    ->required(),

                TextInput::make('borrower_first_name')
                    ->required(),

                TextInput::make('borrower_last_name')
                    ->required(),

                TextInput::make('borrower_phone_number')
                    ->required(),

                TextInput::make('borrower_email')
                    ->email()
                    ->required(),

                DatePicker::make('start_date')
                    ->native(false)
                    ->required(),

                DatePicker::make('end_date')
                    ->native(false)
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
                    ->label('Equipment'),

                TextColumn::make('quantity'),

                TextColumn::make('borrower_full_name'),

                TextColumn::make('start_date')
                    ->date('F d, Y'),

                TextColumn::make('end_date')
                    ->date('F d, Y'),

                TextColumn::make('status')
                    ->formatStateUsing(fn($state): string => Str::headline(BorrowStatus::from($state)->name))
                    ->badge(),
                TextColumn::make('is_returned')
                    ->label('Is returned?')
                    ->badge()
                    ->color(fn($record) => $record === 'Yes' ? 'success' : 'warning'),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Partially Returned with Missing')
                        ->form([
                            Section::make()
                                ->schema([
                                    TextInput::make('quantity_returned')->required(),
                                    TextInput::make('quantity_missing')->required(),
                                ])->columns()
                        ])->action(function (array $data, BorrowedEquipment $borrowedEquipment) {
                            $borrowedEquipment->status = BorrowStatus::PARTIALLY_RETURNED_WITH_MISSING->value;
                            $equipment = $borrowedEquipment->equipment;
                            $quantityMissing = $data['quantity_missing'];
                            $quantityReturned = $data['quantity_returned'];
                            $borrowedEquipment->total_quantity_missing += $quantityMissing;
                            $borrowedEquipment->total_quantity_returned += $quantityReturned;

                            DB::transaction(function () use ($equipment, $borrowedEquipment, $quantityMissing, $quantityReturned) {
                                $status = $equipment->status;
                                // Total Available Quantity 
                                $totalAvailableQuantity = $equipment->quantity_available + $quantityReturned;
                                // Total Returned Quantity 
                                $totalBorrowedQuantity = $equipment->quantity_borrowed - ($quantityMissing + $quantityReturned);
                                // Total Missing Equipment 
                                $totalMissingQuantity = $equipment->quantity_missing + $quantityMissing;

                                if ($totalBorrowedQuantity === 0) $status = EquipmentStatus::ACTIVE->value;

                                $equipment->update([
                                    'quantity_missing' => $totalMissingQuantity,
                                    'quantity_borrowed' => $totalBorrowedQuantity,
                                    'quantity_available' => $totalAvailableQuantity,
                                    'status' => $status
                                ]);

                                $borrowedEquipment->save();

                            });
                        }),
                    Tables\Actions\Action::make('Returned with Missing')
                        ->form([
                            TextInput::make('quantity_missing')->required(),
                        ])->action(function (array $data, BorrowedEquipment $borrowedEquipment) {
                            $quantityMissing = $data['quantity_missing'];
                            $equipment = $borrowedEquipment->equipment;
                            $borrowedEquipment->total_quantity_missing += $quantityMissing;
                            $borrowedEquipment->total_quantity_returned += $borrowedEquipment->quantity - $quantityMissing;
                            $borrowedEquipment->status = BorrowStatus::RETURNED_WITH_MISSING->value;

                            DB::transaction(function () use ($borrowedEquipment, $equipment, $quantityMissing) {
                                $status = $equipment->status;
                                // Total Avaialbel Euqipment
                                $totalAvailableQuantity = $equipment->quantity_available + ($borrowedEquipment->quantity - $quantityMissing);
                                // Total Borrowed Euqiopment
                                $totalBorrowedQuantity = $equipment->quantity_borrowed - $borrowedEquipment->quantity;
                                // Total Missing Euiqpment
                                $totalMissingQuantity = $equipment->quantity_missing + $quantityMissing;

                                if ($totalBorrowedQuantity === 0) $status = EquipmentStatus::ACTIVE->value;

                                $equipment->update([
                                    'quantity_missing' => $totalMissingQuantity,
                                    'quantity_borrowed' => $totalBorrowedQuantity,
                                    'quantity_available' => $totalAvailableQuantity,
                                    'status' => $status
                                ]);

                                $borrowedEquipment->save();
                            });
                        }),
                    Tables\Actions\Action::make('Missing')
                        ->requiresConfirmation()
                        ->modalIconColor('warning')
                        ->color('warning')
                        ->modalHeading('Tag as missing')
                        ->modalDescription('Are you sure you\'d like to tag this as missing? This cannot be undone.')
                        ->modalSubmitActionLabel('Yes, tag as missing')
                        ->action(function (BorrowedEquipment $borrowedEquipment) {
                            $borrowedEquipment->status = BorrowStatus::MISSING->value;
                            $equipment = $borrowedEquipment->equipment;
                            $borrowedEquipment->total_quantity_missing += $borrowedEquipment->quantity;
                            DB::transaction(function () use ($equipment, $borrowedEquipment) {
                                $status = $equipment->status;
                                $totalBorrowedQuantity = $equipment->quantity_borrowed - $borrowedEquipment->quantity;
                                $totalMissingQuantity = $equipment->quantity_missing + $borrowedEquipment->quantity;

                                if ($totalBorrowedQuantity === 0) $status = EquipmentStatus::ACTIVE->value;

                                $equipment->update([
                                    'quantity_missing' => $totalMissingQuantity,
                                    'quantity_borrowed' => $totalBorrowedQuantity,
                                    'status' => $status
                                ]);

                                $borrowedEquipment->save();
                            });
                        }),
                    Tables\Actions\Action::make('Partially Missing')
                        ->form([
                            TextInput::make('quantity_missing')->required(),
                        ])->action(function (array $data, BorrowedEquipment $borrowedEquipment) {
                            $borrowedEquipment->status = BorrowStatus::PARTIALLY_MISSING->value;
                            $quantityMissing = $data['quantity_missing'];
                            $equipment = $borrowedEquipment->equipment;
                            // Getting the total returned equipment
                            $borrowedEquipment->total_quantity_missing += $quantityMissing;
                            DB::transaction(function () use ($equipment, $quantityMissing, $borrowedEquipment) {
                                $status = $equipment->status;
                                if ($borrowedEquipment->total_quantity_missing === $borrowedEquipment->quantity)
                                    $borrowedEquipment->status = BorrowStatus::MISSING->value;

                                $totalBorrowedQuantity = $equipment->quantity_borrowed - $quantityMissing;
                                $totalMissingQuantity = $equipment->quantity_missing + $quantityMissing;

                                if ($totalBorrowedQuantity === 0) $status = EquipmentStatus::ACTIVE->value;

                                $equipment->update([
                                    'quantity_missing' => $totalMissingQuantity,
                                    'quantity_borrowed' => $totalBorrowedQuantity,
                                    'status' => $status
                                ]);

                                $borrowedEquipment->save();
                            });
                        }),

                    Tables\Actions\Action::make('Partially Returned')
                        ->form([
                            TextInput::make('quantity_returned')->required(),
                        ])->action(function (array $data, BorrowedEquipment $borrowedEquipment) {
                            try {
                                // Settting the borrow status to clicked option
                                $borrowedEquipment->status = BorrowStatus::PARTIALLY_RETURNED->value;
                                // Getting the quanttiy returned from data
                                $quantityReturned = $data['quantity_returned'];
                                // Getting the total returned equipment
                                $borrowedEquipment->total_quantity_returned += $quantityReturned;
                                // Getting the equipment model
                                $equipment = $borrowedEquipment->equipment;
                                // GEtting the current status of the equipment
                                $status = $equipment->status;
                                DB::transaction(function () use ($borrowedEquipment, $equipment, $quantityReturned, $status) {
                                    // Checking if the total quantity returned is equal to quantity borrowed
                                    if ($borrowedEquipment->total_quantity_returned === $borrowedEquipment->quantity) {
                                        $borrowedEquipment->status = BorrowStatus::RETURNED->value;
                                        $borrowedEquipment->returned_date = date('Y-m-d');
                                    }
                                    // Getting total available quantity
                                    $totalAvailableQuantity = $equipment->quantity_available + $quantityReturned;
                                    // Getting total borrowed equipment left
                                    $totalBorrowedQuantity = $equipment->quantity_borrowed - $quantityReturned;

                                    // if total borrowed is already zero set the equipment status to active
                                    if ($totalBorrowedQuantity === 0) $status = EquipmentStatus::ACTIVE->value;

                                    $equipment->update([
                                        'quantity_available' => $totalAvailableQuantity,
                                        'quantity_borrowed' => $totalBorrowedQuantity,
                                        'status' => $status
                                    ]);

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
                    Tables\Actions\Action::make('Returned')
                        ->action(function (BorrowedEquipment $borrowedEquipment) {
                            try {
                                $borrowedEquipment->returned_date = date('Y-m-d');
                                $equipment = $borrowedEquipment->equipment;
                                DB::transaction(function () use ($borrowedEquipment, $equipment) {
                                    $borrowedEquipment->save();
                                    $quantity_borrowed = $equipment->quantity_borrowed - $borrowedEquipment->quantity;
                                    $status = EquipmentStatus::ACTIVE->value;
                                    if ($quantity_borrowed > 0)
                                        $status =  EquipmentStatus::PARTIALLY_BORROWED->value;
                                    $equipment->update([
                                        'status' => $status,
                                        'quantity_borrowed' => $quantity_borrowed,
                                        'quantity_available' => $equipment->quantity_available + $borrowedEquipment->quantity
                                    ]);
                                });
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->success()
                                    ->send();
                            }
                        })
                ])->visible(fn($record) => $record->status !== BorrowStatus::RETURNED->value),
            ])
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
            'index' => Pages\ListBorrowedEquipment::route('/'),
            'create' => Pages\CreateBorrowedEquipment::route('/create'),
            'edit' => Pages\EditBorrowedEquipment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('equipment');
    }
}
