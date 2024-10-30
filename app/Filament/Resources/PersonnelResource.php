<?php

namespace App\Filament\Resources;

use App\Enum\Gender;
use App\Filament\Resources\PersonnelResource\Pages;
use App\Filament\Resources\PersonnelResource\RelationManagers;
use App\Models\Personnel;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class PersonnelResource extends Resource
{
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

                Select::make('position_id')
                    ->native(false)
                    ->label('Position')
                    ->relationship('position')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->required(),

                TextInput::make('first_name')
                    ->required(),

                TextInput::make('middle_name')
                    ->nullable(),

                TextInput::make('last_name')
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

                TextArea::make('remarks')
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
                TextColumn::make('department.name'),
                TextColumn::make('position.name'),
                TextColumn::make('phone_number'),
                TextColumn::make('email'),
            ])
            ->filters([
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

                TextEntry::make('department.name')
                    ->label('Department'),

                TextEntry::make('position.name')
                    ->label('Position'),

                TextEntry::make('first_name'),

                TextEntry::make('middle_name'),

                TextEntry::make('last_name'),

                TextEntry::make('gender'),

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
            'index' => Pages\ListPersonnels::route('/'),
            'create' => Pages\CreatePersonnel::route('/create'),
            'edit' => Pages\EditPersonnel::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['position', 'department', 'office']);
    }
}
