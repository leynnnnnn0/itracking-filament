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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Validator;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

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

                Select::make('accounting_officer_id')
                    ->native(false)
                    ->label('Accounting Officer')
                    ->relationship('accounting_officer')
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
                    ->reactive()  // Make it reactive to trigger updates
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('total_amount', ($get('quantity') ?? 0) * ($state ?? 0));
                    }),

                TextInput::make('total_amount')
                    ->numeric()
                    ->disabled()  // Make it read-only
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
                //
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
}
