<?php

namespace App\Filament\Resources;

use App\Enum\Gender;
use App\Filament\Resources\PersonnelResource\Pages;
use App\Filament\Resources\PersonnelResource\RelationManagers;
use App\Models\Personnel;
use App\Traits\HasAuthorizationCheck;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PersonnelResource extends Resource
{
    use HasAuthorizationCheck;
    protected static ?string $model = Personnel::class;
    protected static ?string $navigationGroup = 'People';
    protected static ?string $navigationLabel = 'Personnel';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

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

                Select::make('sub_icsmfr_id')
                    ->native(false)
                    ->label('sub ics/mr')
                    ->relationship('sub_icsmfr')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name),

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

                TextInput::make('office_phone')
                    ->required()
                    ->numeric()
                    ->regex('/^09\d{9}$/')
                    ->extraInputAttributes([
                        'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                        'oninput' => 'this.value = this.value.slice(0, 11)',
                        'inputmode' => 'numeric'
                    ])
                    ->maxLength(11),

                TextInput::make('office_email')
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

                TextArea::make('remarks')
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
                TextColumn::make('office_phone'),
                TextColumn::make('office_email'),
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

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->action(function (Model $record) {
                            if ($record->equipment()->exists()) {
                                Notification::make()
                                    ->title('Deletion Failed')
                                    ->body('Cannot delete this personnel because it has associated equipment.')
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
                        ->modalHeading('Delete personnel')
                        ->modalDescription('Are you sure you\'d like to delete this personnel?')
                        ->modalSubmitActionLabel('Yes, Delete it'),

                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Archive')
                        ->modalHeading('Archive Personnel')
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

                TextEntry::make('sub_icsmfr.name')
                    ->label('Sub ICS/MR'),


                TextEntry::make('first_name'),

                TextEntry::make('middle_name'),

                TextEntry::make('last_name'),


                TextEntry::make('office_email'),

                TextEntry::make('office_phone'),

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
            'index' => Pages\ListPersonnels::route('/'),
            'create' => Pages\CreatePersonnel::route('/create'),
            'edit' => Pages\EditPersonnel::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['sub_icsmfr', 'position', 'department', 'office']);
    }
}
