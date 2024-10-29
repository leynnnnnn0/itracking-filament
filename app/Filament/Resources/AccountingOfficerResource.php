<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountingOfficerResource\Pages;
use App\Filament\Resources\AccountingOfficerResource\RelationManagers;
use App\Models\AccountableOfficer;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountingOfficerResource extends Resource
{
    protected static ?string $model = AccountableOfficer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
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
                TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('office.name'),
                TextColumn::make('phone_number'),
                TextColumn::make('email'),
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
                TextEntry::make('office.name')
                    ->label('Office'),

                TextEntry::make('first_name'),

                TextEntry::make('middle_name'),

                TextEntry::make('last_name'),

                TextEntry::make('email'),

                TextEntry::make('phone_number'),

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
