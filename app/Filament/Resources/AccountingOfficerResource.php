<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountingOfficerResource\Pages;
use App\Filament\Resources\AccountingOfficerResource\RelationManagers;
use App\Models\AccountingOfficer;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountingOfficerResource extends Resource
{
    protected static ?string $model = AccountingOfficer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'People';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('office_id')
                    ->native(false)
                    ->label('Office')
                    ->relationship('office')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name),

                TextInput::make('first_name')
                    ->required(),

                TextInput::make('middle_name')
                    ->nullable(),

                TextInput::make('last_name'),

                TextInput::make('phone_number')
                    ->required()
                    ->numeric()
                    ->regex('/^09\d{9}$/')
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                    ])
                    ->maxLength(11),

                TextInput::make('email')
                    ->required()
                    ->email(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name'),
                TextColumn::make('office.name'),
                TextColumn::make('phone_number'),
                TextColumn::make('email'),
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
            'index' => Pages\ListAccountingOfficers::route('/'),
            'create' => Pages\CreateAccountingOfficer::route('/create'),
            'edit' => Pages\EditAccountingOfficer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('office');
    }
}
