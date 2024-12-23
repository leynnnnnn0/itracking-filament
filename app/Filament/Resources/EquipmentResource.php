<?php

namespace App\Filament\Resources;

use App\BorrowStatus;
use App\Enum\EquipmentStatus;
use App\Filament\Resources\EquipmentResource\Pages;
use App\Models\AccountableOfficer;
use App\Models\BorrowedEquipment;
use App\Models\Equipment;
use App\Models\MissingEquipment;
use App\Models\OfficeAgency;
use App\Models\Personnel;
use App\Models\Unit;
use Carbon\Carbon;
use Exception;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;
    protected static ?string $navigationGroup = 'Equipment';
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationPosition = '0';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('previous_personnel')
                    ->formatStateUsing(function ($state, $record) {

                        return $state ?? $record?->personnel->full_name;
                    })->dehydrated(true),

                Hidden::make('previous_personnel_id')
                    ->formatStateUsing(function ($state, $record) {

                        return $state ?? $record?->personnel->id;
                    })->dehydrated(true),

                Hidden::make('previous_accountable_officer')
                    ->formatStateUsing(function ($state, $record) {
                        return $state ?? $record?->accountable_officer->full_name;
                    })->dehydrated(true),

                Hidden::make('previous_accountable_officer_id')
                    ->formatStateUsing(function ($state, $record) {
                        return $state ?? $record?->accountable_officer->id;
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

                Select::make('sub_icsmfr_id')
                    ->native(false)
                    ->label('Sub ICS/MR')
                    ->relationship('sub_icsmfr')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name),

                TextInput::make('property_number')
                    ->required()
                    ->prefix('PN')
                    ->maxLength(18)
                    ->numeric()
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                    ])

                    ->unique('equipment', 'property_number', ignoreRecord: true)
                    ->minLength(14),

                Select::make('unit')
                    ->options(Unit::select('name')->pluck('name', 'name'))
                    ->native(false)
                    ->createOptionForm([
                        TextInput::make('name')
                            ->unique('units', 'name')
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): string {
                        return Unit::create($data)->name;
                    })
                    ->required(),

                DatePicker::make('date_acquired')
                    ->closeOnDateSelection()
                    ->required()
                    ->beforeOrEqual(function (string $operation, $record) {
                        if ($operation === 'edit') {
                            return $record->date_acquired->endOfDay();
                        }
                        return now()->endOfDay();
                    })
                    ->native(false),

                DatePicker::make('estimated_useful_time')
                    ->extraInputAttributes(['type' => 'month'])
                    ->afterStateHydrated(function ($component, $state) {
                        if ($state) {
                            $component->state(Carbon::parse($state)->format('Y-m'));
                        }
                    })
                    ->dehydrateStateUsing(
                        fn($state) =>
                        $state ? Carbon::createFromFormat('Y-m', $state)->endOfMonth()->format('Y-m-d') : null
                    )
                    ->after('today')
                    ->required(),

                Hidden::make('previous_quantity')
                    ->formatStateUsing(function ($state, $record) {
                        return $state ?? $record?->quantity;
                    })
                    ->dehydrated(true),

                TextInput::make('quantity')
                    ->maxLength(7)
                    ->minValue(1)
                    ->required()
                    ->numeric()
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189 && event.keyCode !== 190 && event.keyCode !== 110)',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get, $record) {
                        $set('total_amount', (float)($state ?? 0) * (float)($get('unit_price') ?? 0));
                        if (!$record) {
                            $set('quantity_available', (float)($record?->quantity_available ?? 0) + (float)($state ?? 0));
                        }
                        if ($state && $record) {
                            $set('previous_quantity', $record->quantity);
                        }
                    }),


                Hidden::make('quantity_available')
                    ->dehydrated(true)
                    ->live()
                    ->required()
                    ->live(),

                TextInput::make('unit_price')
                    ->numeric()
                    ->live()
                    ->minValue(1)
                    ->maxValue(99999999)
                    ->required()
                    ->live()
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189 && event.keyCode !== 190 && event.keyCode !== 110)',
                    ])
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('total_amount', (float)($get('quantity') ?? 0)  * (float)($state ?? 0));
                    }),

                TextInput::make('total_amount')
                    ->numeric()
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189 && event.keyCode !== 190 && event.keyCode !== 110)',
                    ])
                    ->minValue(1)
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
                TextColumn::make('id'),

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
                    ->formatStateUsing(fn($state): string => Str::replace('_', ' ', Str::title(EquipmentStatus::from($state)->name)))
                    ->badge()
                    ->color(fn(string $state): string => EquipmentStatus::from($state)->getColor()),
            ])
            ->filters([
                TrashedFilter::make()
                    ->visible(Auth::user()->role === 'Admin'),
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
                // Borrow Form
                Tables\Actions\Action::make('Borrow')
                    ->visible(fn($record) => $record->deleted_at === null && $record->quantity_available > 0)
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

                                Select::make('office_agency_id')
                                    ->label('Office/Agency')
                                    ->options(OfficeAgency::select(['name', 'id'])->pluck('name', 'id'))
                                    ->native(false)
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data): string {
                                        return OfficeAgency::create($data)->id;
                                    })
                                    ->required(),

                                TextInput::make('quantity')
                                    ->integer()
                                    ->maxLength(7)
                                    ->required()
                                    ->extraInputAttributes([
                                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189 && event.keyCode !== 190 && event.keyCode !== 110)',
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
                                        'oninput' => 'this.value = this.value.slice(0, 11)',
                                        'inputmode' => 'numeric'
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

                                TextArea::make('remarks')
                                    ->rules([
                                        'string',
                                        'regex:/[a-zA-Z]/',
                                    ])
                                    ->extraAttributes(['class' => 'resize-none']),

                                Hidden::make('status')
                                    ->default(BorrowStatus::BORROWED->value)
                                    ->dehydrated(true)
                                    ->required()
                            ])->columns(2),

                    ])->action(function (array $data) {
                        try {
                            DB::transaction(function () use ($data) {
                                $borrowedEquipment = BorrowedEquipment::create([
                                    'office_agency_id' => $data['office_agency_id'],
                                    'equipment_id' => $data['equipment_id'],
                                    'quantity' => $data['quantity'],
                                    'borrower_first_name' => $data['borrower_first_name'],
                                    'borrower_last_name' => $data['borrower_last_name'],
                                    'borrower_phone_number' => $data['borrower_phone_number'],
                                    'borrower_email' => $data['borrower_email'],
                                    'start_date' => $data['start_date'],
                                    'end_date' => $data['end_date'],
                                    'remarks' => $data['remarks']
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
                    }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn($record) => $record->deleted_at === null),

                    Tables\Actions\DeleteAction::make()
                        ->label('Archive')
                        ->modalHeading('Archive Equipment')
                        ->successNotificationTitle('Archived')
                        ->action(function (Model $record) {
                            $hasBorrowedEquipment = BorrowedEquipment::where('equipment_id', $record->id)->exists();
                            if ($hasBorrowedEquipment) {
                                Notification::make()
                                    ->title('Unable to Archive Equipment')
                                    ->body('This equipment cannot be archived because it is currently associated with borrowed equipment.')
                                    ->danger()
                                    ->send();
                                return false;
                            }

                            $hasMissingEquipment = MissingEquipment::where('equipment_id', $record->id)->exists();
                            if ($hasMissingEquipment) {
                                Notification::make()
                                    ->title('Unable to Archive Equipment')
                                    ->body('This equipment cannot be archived because it is currently marked as missing.')
                                    ->danger()
                                    ->send();
                                return false;
                            }

                            Notification::make()
                                ->title('Archived')
                                ->success()
                                ->send();

                            $record->delete();
                        }),
                    Tables\Actions\RestoreAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // DeleteBulkAction::make()
                    //     ->label('Archive')
                    //     ->modalHeading('Archive Equipment')
                    //     ->successNotificationTitle('Archived'),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Basic Details')->schema([
                    TextEntry::make('id'),
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

                    TextEntry::make('sub_icsmfr.full_name')
                        ->label('Sub ICS/MR'),


                ])->columns(2),

                Section::make('Equipment History')
                    ->schema([
                        RepeatableEntry::make('equipment_history')
                            ->label('Historical Records')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Date Assigned')
                                    ->date('F d, Y')
                                    ->columnSpan(2),
                                TextEntry::make('personnel.full_name')
                                    ->label('Responsible Person'),
                                TextEntry::make('accountable_officer.full_name')
                                    ->label('Accountable Officer'),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->grid(false)
                    ])
                    ->collapsible()
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
        return parent::getEloquentQuery()->with(['personnel', 'accountable_officer', 'organization_unit', 'operating_unit_project', 'fund', 'sub_icsmfr'])
            ->orderBy('quantity');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
