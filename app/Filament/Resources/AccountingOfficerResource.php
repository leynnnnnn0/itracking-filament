<?php

namespace App\Filament\Resources;

use App\Enum\Gender;
use App\Filament\Resources\AccountingOfficerResource\Pages;
use App\Models\AccountableOfficer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->required(),

                Select::make('department_id')
                    ->native(false)
                    ->label('Department')
                    ->relationship('department')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->required(),

                Select::make('position_id')
                    ->native(false)
                    ->label('Position')
                    ->relationship('position')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->required(),

                TextInput::make('first_name')
                    ->maxLength(30)
                    ->required(),

                TextInput::make('middle_name')
                    ->maxLength(30)
                    ->nullable(),

                TextInput::make('last_name')
                    ->maxLength(30)
                    ->required(),

                Select::make('gender')
                    ->options(Gender::class)
                    ->enum(Gender::class)
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

                DatePicker::make('start_date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->default(date('Y-m-d'))
                    ->required(),

                DatePicker::make('end_date')
                    ->native(false)
                    ->after('start_date')
                    ->closeOnDateSelection()
                    ->required(),

                Textarea::make('remarks')
                    ->extraAttributes(['class' => 'resize-none'])
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
                SelectFilter::make('office')
                    ->multiple()
                    ->relationship('office', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Model $record) {
                        if ($record->equipment()->exists()) {
                            Notification::make()
                                ->title('Deletion Failed')
                                ->body('Cannot delete this accountable officer because it has associated equipment.')
                                ->danger()
                                ->send();

                            return false;
                        }

                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->modalIconColor('danger')
                    ->color('danger')
                    ->modalHeading('Delete accountable officer')
                    ->modalDescription('Are you sure you\'d like to delete this accountable officer?')
                    ->modalSubmitActionLabel('Yes, Delete it')
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
        return parent::getEloquentQuery()->with(['position', 'department', 'office']);
    }
}
