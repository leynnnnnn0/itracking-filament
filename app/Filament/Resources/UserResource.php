<?php

namespace App\Filament\Resources;

use App\Enum\Gender;
use App\Enum\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'People';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Details')
                    ->schema([
                        TextInput::make('first_name')
                            ->required(),

                        TextInput::make('middle_name')
                            ->nullable(),

                        TextInput::make('last_name'),

                        Select::make('gender')
                            ->options(Gender::values())
                            ->required(),

                        DatePicker::make('date_of_birth')
                            ->required()
                            ->native(false),

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

                        Select::make('Role')
                            ->options(UserRole::values())
                            ->native(false)
                            ->required(),

                        TextInput::make('password')
                            ->rules(['min:8'])
                            ->password()
                            ->revealable()
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name'),
                TextColumn::make('email'),
                TextColumn::make('phone_number'),
                TextColumn::make('role')->badge()
                    ->color(fn(string $state): string => UserRole::from($state)->getColor())
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
