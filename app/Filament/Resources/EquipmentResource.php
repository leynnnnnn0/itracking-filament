<?php

namespace App\Filament\Resources;

use App\Enum\EquipmentStatus;
use App\Enum\Unit;
use App\Filament\Resources\EquipmentResource\Pages;
use App\Filament\Resources\EquipmentResource\RelationManagers;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Validator;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;
    protected static ?string $navigationGroup = 'Equipment';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
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

                Select::make('personal_protective_equipment_id')
                    ->native(false)
                    ->label('Personal Protective Equipment')
                    ->relationship('personal_protective_equipment')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->required(),

                TextInput::make('property_number')
                    ->required(),

                Select::make('unit')
                    ->options(Unit::values())
                    ->native(false)
                    ->required(),

                DatePicker::make('date_acquired')
                    ->native(false),

                DatePicker::make('estimated_useful_time')
                    ->native()
                    ->extraInputAttributes(['type' => 'month']),

                TextInput::make('quantity')
                    ->numeric()
                    ->live()
                    ->required()
                    ->reactive()  // Make it reactive to trigger updates
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('total_amount', ($state ?? 0) * ($get('unit_price') ?? 0));
                    }),

                TextInput::make('unit_price')
                    ->numeric()
                    ->live()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('total_amount', ($get('quantity') ?? 0) * ($state ?? 0));
                    }),

                TextInput::make('total_amount')
                    ->numeric()
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

                TextColumn::make('property_number'),

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
                //
            ])
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
                    TextEntry::make('quantity_condemned'),

                    TextEntry::make('quantity_condemned'),

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

                    TextEntry::make('personal_protective_equipment.name'),

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
        return parent::getEloquentQuery()->with(['personnel', 'accountable_officer', 'organization_unit', 'operating_unit_project', 'fund', 'personal_protective_equipment']);
    }
}
