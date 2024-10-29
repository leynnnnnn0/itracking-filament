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
                        $equipment = Equipment::find($value)?->name;
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
                    Tables\Actions\Action::make('Partially Borrowed')
                        ->action(function (BorrowedEquipment $borrowedEquipment){
                            
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
                        }),
                ])
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
