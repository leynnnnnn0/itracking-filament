<?php

namespace App\Filament\Resources;

use App\BorrowStatus;
use App\Enum\EquipmentStatus;
use App\Enum\MissingStatus;
use App\Filament\Resources\MissingEquipmentResource\Pages;
use App\Filament\Resources\MissingEquipmentResource\RelationManagers;
use App\Models\Equipment;
use App\Models\MissingEquipment;
use App\Traits\HasModelStatusIdentifier;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MissingEquipmentResource extends Resource
{
    use HasModelStatusIdentifier;
    protected static ?string $model = MissingEquipment::class;
    protected static ?string $navigationGroup = 'Equipment';
    protected static ?string $navigationLabel = 'Missing Equipment';
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
                    ->live()
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
                        $equipmentId = $get('equipment_id');
                        $quantityAvailable = Equipment::find($equipmentId)?->quantity_available;

                        return $quantityAvailable ? 'Available: ' . $quantityAvailable : '';
                    })
                    ->minValue(1)
                    ->maxValue(function (callable $get) {
                        $equipmentId = $get('equipment_id');
                        $quantityAvailable = Equipment::find($equipmentId)?->quantity_available;

                        return  $quantityAvailable ?? 0;
                    }),

                Select::make('status')
                    ->disabled(fn(string $operation): bool => $operation === 'edit')
                    ->live()
                    ->native(false)
                    ->options(MissingStatus::values())
                    ->default('Reported')
                    ->required(),

                TextInput::make('reported_by')
                    ->rules([
                        'string',
                        'regex:/^[a-zA-Z\s]+$/',
                    ])
                    ->maxLength(30)
                    ->required(),

                DatePicker::make('reported_date')
                    ->native(false)
                    ->default(today())
                    ->beforeOrEqual('today')
                    ->required(),

                Textarea::make('remarks')
                    ->extraAttributes(['class' => 'resize-none']),

                Radio::make('is_condemned')
                    ->label('Is condemned?')
                    ->live()
                    ->hidden(fn(Get $get) => $get('status') !== 'Reported to SPMO')
                    ->boolean()
                    ->inline()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('equipment.name')
                    ->label('Equipment Name'),

                TextColumn::make('equipment.property_number')
                    ->label('Property Number'),

                TextColumn::make('quantity')
                    ->label('Missing Quantity'),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('reported_by'),

                TextColumn::make('reported_date')
                    ->date('F d, Y'),

                TextColumn::make('is_condemned')
                    ->formatStateUsing(fn($record) => $record->is_condemned ? 'Yes' : 'No'),
            ])
            ->filters([
                TrashedFilter::make()
                    ->visible(Auth::user()->role === 'Admin'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('found')
                        ->visible(fn($record) => $record->status !== 'Found')
                        ->color('success')
                        ->form([
                            TextInput::make('quantity_found')
                                ->integer()
                                ->extraInputAttributes([
                                    'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                                ])
                                ->minValue(1)
                                ->required()
                                ->maxValue(fn($record) => $record->quantity - $record->quantity_found)
                                ->hint(fn($record) => 'Missing Quantity: ' . $record->quantity - $record->quantity_found)
                        ])
                        ->requiresConfirmation()
                        ->modalDescription('Please confirm that the quantity provided is accurate before proceeding. This action will update the equipment details record accordingly.')
                        ->modalSubmitActionLabel('Submit')
                        ->action(function (array $data, Model $record) {
                            try {
                                DB::transaction(function () use ($record, $data) {
                                    $equipment = $record->equipment;
                                    $quantityFound = $data['quantity_found'];
                                    if ($record->borrowed_equipment()->exists()) {
                                        $record->borrowed_equipment->total_quantity_missing -= $quantityFound;
                                        $record->borrowed_equipment->total_quantity_returned += $quantityFound;
                                        $record->borrowed_equipment->status = self::getBorrowStatus($record->borrowed_equipment);
                                        $record->borrowed_equipment->save();
                                    }
                                    $equipment->quantity_missing -= $quantityFound;
                                    $equipment->quantity_available += $quantityFound;
                                    $record->quantity_found += $quantityFound;

                                    if ($record->quantity_found === $record->quantity) {
                                        $record->status = MissingStatus::FOUND->value;
                                    }
                                    $equipment->status = self::getEquimentStatus($equipment);
                                    $record->save();
                                    $equipment->save();
                                });
                                Notification::make()
                                    ->title('Success')
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

                        ->visible(fn($record) => $record->status !== 'Found' && !$record->is_condemned),

                    Tables\Actions\Action::make('reported_to_spmo')
                        ->label('Reported to SPMO')
                        ->requiresConfirmation()
                        ->modalIconColor('warning')
                        ->color('warning')
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you\'d like to tag this as Reported to SPMO?')
                        ->modalSubmitActionLabel('Yes')
                        ->action(function (Model $record) {
                            $record->update([
                                'status' => MissingStatus::REPORTED_TO_SPMO->value
                            ]);
                            Notification::make()
                                ->title('Updated Successfully')
                                ->body('Status Changed to Reported to SPMO.')
                                ->success()
                                ->send();
                        })->visible(fn($record) => $record->status === MissingStatus::REPORTED->value && $record->status !== 'Found' && !$record->is_condemned),

                    Tables\Actions\Action::make('condemned')
                        ->requiresConfirmation()
                        ->modalIconColor('danger')
                        ->color('danger')
                        ->modalHeading('Confirmation')
                        ->modalDescription('Are you sure you\'d like to tag this as condemned?')
                        ->modalSubmitActionLabel('Yes')
                        ->action(function (Model $record) {
                            try {
                                DB::transaction(function () use ($record) {
                                    $record->update([
                                        'is_condemned' => true
                                    ]);
                                    $equipment =  $record->equipment;
                                    $equipment->quantity_missing -= $record->quantity - $record->quantity_found;
                                    $equipment->quantity_condemned += $record->quantity - $record->quantity_found;
                                    $equipment->status = self::getEquimentStatus($equipment);
                                    $equipment->save();
                                });


                                Notification::make()
                                    ->title('Updated Successfully')
                                    ->body('Mark as Condemned.')
                                    ->success()
                                    ->send();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->success()
                                    ->send();
                            }
                        })->visible(fn($record) => $record->status == MissingStatus::REPORTED_TO_SPMO->value && !$record->is_condemned && $record->status !== 'Found' && !$record->is_condemned),

                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('pdf')
                        ->label('PDF')
                        ->color('success')
                        ->icon('heroicon-o-arrow-down')
                        ->action(function (Model $record) {
                            return response()->streamDownload(function () use ($record) {
                                echo Pdf::loadHtml(
                                    Blade::render('pdf.missing-equipment', ['equipment' => $record])
                                )->stream();
                            },  'missing-equipment.pdf');
                        }),
                    Tables\Actions\EditAction::make()
                        ->visible(fn($record) => $record->status !== 'Found' && !$record->is_condemned),
                    Tables\Actions\DeleteAction::make()
                        ->label('Archive')
                        ->modalHeading('Archive Missing Equipment')
                        ->successNotificationTitle('Archived')
                        ->label('Archive')
                        ->visible(fn($record) => $record->is_condemned || $record->quantity === $record->quantity_found),
                    Tables\Actions\RestoreAction::make(),
                ])
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make()
                //         ->label('Archive')
                //         ->modalHeading('Archive Missing Equipment')
                //         ->successNotificationTitle('Archived'),
                //     Tables\Actions\RestoreBulkAction::make(),
                // ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Equipment Details')
                    ->schema([
                        TextEntry::make('equipment.id')
                            ->label('Id'),

                        TextEntry::make('equipment.name')
                            ->label('Name'),

                        TextEntry::make('equipment.description')
                            ->label('Description'),

                        TextEntry::make('equipment.property_number')
                            ->label('Property Number'),

                        TextEntry::make('equipment.unit')
                            ->label('Unit'),

                        TextEntry::make('equipment.quantity')
                            ->label('Quantity'),

                        TextEntry::make('equipment.quantity_available')
                            ->label('Available Quantity'),

                        TextEntry::make('equipment.quantity_borrowed')
                            ->label('Borrowed Quantity'),

                        TextEntry::make('equipment.quantity_missing')
                            ->label('Missing Quantity'),

                        TextEntry::make('equipment.quantity_condemned')
                            ->label('Condemned Quantity'),

                        TextEntry::make('equipment.date_acquired')
                            ->label('Date Acquired')
                            ->date('F d, Y'),

                        TextEntry::make('estimated_useful_time')
                            ->label('Estimated Useful Time')
                            ->date('Y-m'),

                        TextEntry::make('equipment.unit_price')
                            ->label('Unit Price'),

                        TextEntry::make('equipment.total_amount')
                            ->label('Total Amount'),

                        TextEntry::make('equipment.status')
                            ->label('Status')
                            ->formatStateUsing(fn($state): string => Str::headline(EquipmentStatus::from($state)->name))
                            ->badge()
                            ->color(fn(string $state): string => EquipmentStatus::from($state)->getColor()),

                    ])->columns(2),

                Section::make('Report Details')
                    ->schema([
                        TextEntry::make('quantity')
                            ->label('Missing Quantity'),
                        TextEntry::make('status'),

                        TextEntry::make('quantity_found'),

                        TextEntry::make('status'),

                        TextEntry::make('reported_by'),

                        TextEntry::make('reported_date')
                            ->date('F d, Y'),

                        TextEntry::make('description'),

                        TextEntry::make('is_condemned')
                            ->formatStateUsing(fn($record) => $record->is_condemned ? 'Yes' : 'No'),
                    ])->columns(2),
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
            'index' => Pages\ListMissingEquipment::route('/'),
            'create' => Pages\CreateMissingEquipment::route('/create'),
            'edit' => Pages\EditMissingEquipment::route('/{record}/edit'),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('new_missing_equipment')
                ->label('New missing equipment')
                ->color('success')
                ->icon('heroicon-o-plus'),
            Action::make('additional_action')
                ->label('Additional Action')
                ->color('primary')
                ->icon('heroicon-o-plus'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('equipment')->latest();
    }
}
