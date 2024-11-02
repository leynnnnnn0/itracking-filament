<?php

namespace App\Filament\Resources;

use App\BorrowStatus;
use App\Enum\EquipmentStatus;
use App\Filament\Resources\EquipmentResource\Pages;
use App\Models\AccountableOfficer;
use App\Models\BorrowedEquipment;
use App\Models\Equipment;
use App\Models\Personnel;
use App\Models\Unit;
use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;
    protected static ?string $navigationGroup = 'Equipment';
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('previous_personnel')
                    ->formatStateUsing(function ($state, $record) {

                        return $state ?? $record?->personnel->full_name;
                    })->dehydrated(true),

                TextInput::make('name')
                    ->rules([
                        'string',
                        'regex:/[a-zA-Z]/',
                    ])
                    ->maxLength(30)
                    ->label('Equipment Name')
                    ->required(),

                Textarea::make('description')
                    ->extraAttributes(['class' => 'resize-none']),

                Select::make('accountable_officer_id')
                    ->native(false)
                    ->label('Accountable Officer')
                    ->relationship('accountable_officer')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                    ->required(),

                Select::make('personnel_id')
                    ->native(false)
                    ->label('Personnel')
                    ->relationship('personnel')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name)
                    ->required(),

                Select::make('organization_unit_id')
                    ->native(false)
                    ->label('Organization Unit')
                    ->relationship('organization_unit')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->required(),

                Select::make('operating_unit_project_id')
                    ->native(false)
                    ->label('Operating Unit Project')
                    ->relationship('operating_unit_project')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->required(),

                Select::make('fund_id')
                    ->native(false)
                    ->label('Fund')
                    ->relationship('fund')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->required(),

                TextInput::make('property_number')
                    ->required()
                    ->placeholder('PN****************')
                    ->regex('/^PN[a-zA-Z0-9]{14,18}$/')
                    ->maxLength(20)
                    ->minLength(16),

                Select::make('unit')
                    ->options(Unit::select('name')->pluck('name', 'name'))
                    ->native(false)
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): string {
                        return Unit::create($data)->name;
                    })
                    ->required(),

                DatePicker::make('date_acquired')
                    ->closeOnDateSelection()
                    ->required()
                    ->native(false),

                DatePicker::make('estimated_useful_time')
                    ->extraInputAttributes(['type' => 'month'])
                    ->formatStateUsing(
                        fn($record) => $record && $record->estimated_useful_time
                            ? Carbon::parse($record->estimated_useful_time)->format('Y-m')
                            : null
                    )
                    ->required(),


                TextInput::make('quantity')
                    ->maxLength(7)
                    ->numeric()
                    ->live()
                    ->required()
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                    ])
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get, $record) {
                        $set('total_amount', ($state ?? 0) * ($get('unit_price') ?? 0));
                        $set('quantity_available', ($record?->quantity_available ?? 0) + ($state ?? 0));
                    }),


                Hidden::make('quantity_available')
                    ->dehydrated(true)
                    ->live()
                    ->required()
                    ->reactive(),

                TextInput::make('unit_price')
                    ->numeric()
                    ->live()
                    ->maxValue(99999999)
                    ->required()
                    ->reactive()
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                    ])
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('total_amount', ($get('quantity') ?? 0) * ($state ?? 0));
                    }),

                TextInput::make('total_amount')
                    ->numeric()
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                    ])
                    ->required(),

                Select::make('status')
                    ->options(EquipmentStatus::values())
                    ->required()
                    ->hidden()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('property_number')
                    ->searchable(),

                TextColumn::make('quantity'),

                TextColumn::make('quantity_available')
                    ->label('Available'),

                TextColumn::make('quantity_borrowed')
                    ->label('Borrowed'),

                TextColumn::make('quantity_missing')
                    ->label('Missing'),

                TextColumn::make('quantity_condemned')
                    ->label('Condemned'),

                TextColumn::make('status')

                    ->formatStateUsing(fn($state): string => Str::headline(EquipmentStatus::from($state)->name))
                    ->badge()
                    ->color(fn(string $state): string => EquipmentStatus::from($state)->getColor()),
            ])
            ->filters([
                SelectFilter::make('responsible_person')
                    ->relationship('personnel', 'id')
                    ->getSearchResultsUsing(function (string $search) {
                        return Personnel::query()
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn($personnel) => [$personnel->id => $personnel->full_name])
                            ->toArray();
                    })
                    ->getOptionLabelUsing(fn($value): ?string => Personnel::find($value)?->full_name)
                    ->searchable(),

                SelectFilter::make('accountable_officer')
                    ->relationship('accountable_officer', 'id')
                    ->getSearchResultsUsing(function (string $search) {
                        return AccountableOfficer::query()
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn($accountable_officer) => [$accountable_officer->id => $accountable_officer->full_name])
                            ->toArray();
                    })
                    ->getOptionLabelUsing(fn($value): ?string => AccountableOfficer::find($value)?->full_name)
                    ->searchable(),

                TernaryFilter::make('available')
                    ->label('Available Equipment')
                    ->queries(
                        true: fn(Builder $query) => $query->where('quantity_available', '>', 0),
                        false: fn(Builder $query) => $query->where('quantity_available', '=', 0),
                    ),

                TernaryFilter::make('borrowed')
                    ->label('Borrowed Equipment')
                    ->queries(
                        true: fn(Builder $query) => $query->where('quantity_borrowed', '>', 0),
                        false: fn(Builder $query) => $query->where('quantity_borrowed', '=', 0),
                    ),

                TernaryFilter::make('missing')
                    ->label('Missing Equipment')
                    ->queries(
                        true: fn(Builder $query) => $query->where('quantity_missing', '>', 0),
                        false: fn(Builder $query) => $query->where('quantity_missing', '=', 0),
                    ),

                TernaryFilter::make('condemned')
                    ->label('Condemned Equipment')
                    ->queries(
                        true: fn(Builder $query) => $query->where('quantity_condemned', '>', 0),
                        false: fn(Builder $query) => $query->where('quantity_condemned', '=', 0),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                // Borrow Form
                Tables\Actions\Action::make('Borrow')
                    ->color('warning')
                    ->form([
                        \Filament\Forms\Components\Section::make()
                            ->schema([
                                Select::make('equipment_id')
                                    ->native(false)
                                    ->label('Equipment')
                                    ->searchable()
                                    ->getOptionLabelUsing(function ($value): ?string {
                                        $equipment = Equipment::find($value);
                                        return "$equipment->name (PN: $equipment->property_number)";
                                    })
                                    ->default(fn($record) => $record->id)
                                    ->required(),

                                TextInput::make('quantity')
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


                                TextInput::make('borrower_first_name')
                                    ->maxLength(30)
                                    ->required(),

                                TextInput::make('borrower_last_name')
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


                                Hidden::make('status')
                                    ->default(BorrowStatus::BORROWED->value)
                                    ->dehydrated(true)
                                    ->required()
                            ])->columns(2)
                    ])->action(function (array $data) {
                        try {
                            DB::transaction(function () use ($data) {
                                $borrowedEquipment = BorrowedEquipment::create([
                                    'equipment_id' => $data['equipment_id'],
                                    'quantity' => $data['quantity'],
                                    'borrower_first_name' => $data['borrower_first_name'],
                                    'borrower_last_name' => $data['borrower_last_name'],
                                    'borrower_phone_number' => $data['borrower_phone_number'],
                                    'borrower_email' => $data['borrower_email'],
                                    'start_date' => $data['start_date'],
                                    'end_date' => $data['end_date'],
                                ]);
                                $equipment = $borrowedEquipment->equipment;

                                $totalAvailableEquipment = $equipment->quantity_available - $borrowedEquipment->quantity;
                                $totalBorrowedEquipment =  $equipment->quantity_borrowed + $borrowedEquipment->quantity;
                                $status =  EquipmentStatus::PARTIALLY_BORROWED->value;
                                
                                if ($totalBorrowedEquipment === $equipment->quantity_available)
                                    $status = EquipmentStatus::FULLY_BORROWED->value;

                                $equipment->update([
                                    'quantity_available' => $totalAvailableEquipment,
                                    'quantity_borrowed' => $totalBorrowedEquipment,
                                    'status' => $status
                                ]);

                                Notification::make()
                                    ->title('Success')
                                    ->body('Borrow Log Created.')
                                    ->success()
                                    ->send();
                            });
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->success()
                                ->send();
                        }
                    })
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
                Section::make('Basic Details')->schema([
                    TextEntry::make('name'),

                    TextEntry::make('description'),

                    TextEntry::make('property_number'),

                    TextEntry::make('unit'),

                    TextEntry::make('quantity'),

                    TextEntry::make('quantity_available'),

                    TextEntry::make('quantity_borrowed'),

                    TextEntry::make('quantity_missing'),

                    TextEntry::make('quantity_condemned'),

                    TextEntry::make('date_acquired')

                        ->date('F d, Y'),
                    TextEntry::make('estimated_useful_time')

                        ->date('Y-m'),

                    TextEntry::make('unit_price'),

                    TextEntry::make('total_amount'),

                    TextEntry::make('status')

                        ->formatStateUsing(fn($state): string => Str::headline(EquipmentStatus::from($state)->name))
                        ->badge()
                        ->color(fn(string $state): string => EquipmentStatus::from($state)->getColor()),
                ])->columns(2),
                Section::make('Others')->schema([
                    TextEntry::make('personnel.full_name'),

                    TextEntry::make('accountable_officer.full_name'),

                    TextEntry::make('organization_unit.name'),

                    TextEntry::make('operating_unit_project.name'),

                    TextEntry::make('fund.name'),


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
            'index' => Pages\ListEquipment::route('/'),
            'create' => Pages\CreateEquipment::route('/create'),
            'edit' => Pages\EditEquipment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['personnel', 'accountable_officer', 'organization_unit', 'operating_unit_project', 'fund'])->orderBy('quantity');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
