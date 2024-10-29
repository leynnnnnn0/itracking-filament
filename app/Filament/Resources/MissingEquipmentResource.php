<?php

namespace App\Filament\Resources;

use App\Enum\MissingStatus;
use App\Filament\Resources\MissingEquipmentResource\Pages;
use App\Filament\Resources\MissingEquipmentResource\RelationManagers;
use App\Models\Equipment;
use App\Models\MissingEquipment;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
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

class MissingEquipmentResource extends Resource
{
    protected static ?string $model = MissingEquipment::class;
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
                    ->getSearchResultsUsing(fn(string $search): array => Equipment::select('name', 'property_number', 'id')->whereAny(['name', 'property_number'], 'like', "%{$search}%")->limit(20)->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),

                TextInput::make('quantity')
                    ->integer()
                    ->required(),

                Select::make('status')
                    ->live()
                    ->native(false)
                    ->options(MissingStatus::values())
                    ->default('Reported')
                    ->required(),

                TextInput::make('reported_by')
                    ->required(),

                DatePicker::make('reported_date')
                    ->native(false)
                    ->required(),

                Textarea::make('remarks')
                    ->extraAttributes(['class' => 'resize-none']),

                Radio::make('is_condemned')
                    ->label('Is condemned?')
                    ->reactive()
                    ->hidden(fn(Get $get) => $get('status') !== 'Reported to SPMO')
                    ->boolean()
                    ->inline()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
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
            'index' => Pages\ListMissingEquipment::route('/'),
            'create' => Pages\CreateMissingEquipment::route('/create'),
            'edit' => Pages\EditMissingEquipment::route('/{record}/edit'),
        ];
    }
}
