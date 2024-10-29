<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalProtectiveEquipmentResource\Pages;
use App\Filament\Resources\PersonalProtectiveEquipmentResource\RelationManagers;
use App\Models\PersonalProtectiveEquipment;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonalProtectiveEquipmentResource extends Resource
{
    protected static ?string $model = PersonalProtectiveEquipment::class;
    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Office Details')
                    ->schema([
                        TextInput::make('name')->required()
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
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
            'index' => Pages\ListPersonalProtectiveEquipment::route('/'),
            'create' => Pages\CreatePersonalProtectiveEquipment::route('/create'),
            'edit' => Pages\EditPersonalProtectiveEquipment::route('/{record}/edit'),
        ];
    }
}
