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
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'People';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Details')
                    ->schema([
                        TextInput::make('first_name')
                            ->rules([
                                'string',
                                'regex:/^[a-zA-Z\s]+$/',
                            ])
                            ->maxLength(30)
                            ->required(),

                        TextInput::make('middle_name')
                            ->rules([
                                'string',
                                'regex:/^[a-zA-Z\s]+$/',
                            ])
                            ->maxLength(30)
                            ->nullable(),

                        TextInput::make('last_name')
                            ->rules([
                                'string',
                                'regex:/^[a-zA-Z\s]+$/',
                            ])
                            ->maxLength(30)
                            ->required(),

                        Select::make('gender')
                            ->native(false)
                            ->options(Gender::values())
                            ->required(),

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
                            ->unique(ignoreRecord: true)
                            ->email(),

                        Select::make('role')
                            ->options(UserRole::values())
                            ->enum(UserRole::class)
                            ->native(false)
                            ->required(),
                    ])->columns(2),

                Section::make('Account Access')
                    ->schema([
                        TextInput::make('password')
                            ->rules(['min:8'])
                            ->label(fn(string $operation): string => $operation === 'create' ? 'Password' : 'New Password')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->revealable(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->searchable(['first_name', 'last_name']),
                TextColumn::make('email'),
                TextColumn::make('phone_number'),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn(string $state): string => UserRole::from($state)->getColor())
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('role')
                    ->native(false)
                    ->options(UserRole::values())
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->color('danger'),
                Tables\Actions\RestoreAction::make()
                    ->requiresConfirmation()
                    ->color('warning'),

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
                TextEntry::make('first_name'),

                TextEntry::make('middle_name'),

                TextEntry::make('last_name'),

                TextEntry::make('gender'),

                TextEntry::make('email'),

                TextEntry::make('phone_number'),

                TextEntry::make('role')
                    ->badge()
                    ->color(fn(string $state): string => UserRole::from($state)->getColor()),

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
