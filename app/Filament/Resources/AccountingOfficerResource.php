<?php

namespace App\Filament\Resources;

use App\Enum\Gender;
use App\Filament\Resources\AccountingOfficerResource\Pages;
use App\Models\AccountableOfficer;
use App\Traits\HasAuthorizationCheck;
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
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AccountingOfficerResource extends Resource
{
    use HasAuthorizationCheck;
    protected static ?string $model = AccountableOfficer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'People';
    protected static bool $shouldRegisterNavigation = false;

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

                Select::make('sex')
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
                    ->rules([
                        'string',
                        'regex:/[a-zA-Z]/',
                    ])
                    ->extraAttributes(['class' => 'resize-none'])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('office.name'),
                TextColumn::make('department.name'),
                TextColumn::make('position.name'),
                TextColumn::make('phone_number'),
                TextColumn::make('email'),
            ])
            ->filters([
                TrashedFilter::make()
                    ->visible(Auth::user()->role === 'Admin'),

                SelectFilter::make('office')
                    ->multiple()
                    ->relationship('office', 'name'),

                SelectFilter::make('department')
                    ->multiple()
                    ->relationship('department', 'name'),

                SelectFilter::make('position')
                    ->multiple()
                    ->relationship('position', 'name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->action(function (Model $record) {
                            if ($record->equipment()->exists()) {
                                Notification::make()
                                    ->title('Archive Failed')
                                    ->body('Cannot Archive this accountable officer because it has associated equipment.')
                                    ->danger()
                                    ->send();

                                return false;
                            }

                            $record->delete();
                        })
                        ->label('Archive')
                        ->requiresConfirmation()
                        ->modalIconColor('danger')
                        ->color('danger')
                        ->modalHeading('Archive accountable officer')
                        ->modalDescription('Are you sure you\'d like to delete this accountable officer?')
                        ->modalSubmitActionLabel('Yes, Archive it'),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Archive')
                        ->modalHeading('Archive Accountable Officers')
                        ->successNotificationTitle('Archived'),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('id'),
                TextEntry::make('office.name')
                    ->label('Office'),

                TextEntry::make('department.name')
                    ->label('Department'),

                TextEntry::make('position.name')
                    ->label('Position'),

                TextEntry::make('first_name'),

                TextEntry::make('middle_name'),

                TextEntry::make('last_name'),

                TextEntry::make('sex'),

                TextEntry::make('email'),

                TextEntry::make('phone_number'),

                TextEntry::make('start_date')
                    ->date('F d, Y'),

                TextEntry::make('end_date')
                    ->date('F d, Y'),

                TextEntry::make('remarks'),

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
        return parent::getEloquentQuery()->with(['position', 'department', 'office', 'equipment']);
    }
}
