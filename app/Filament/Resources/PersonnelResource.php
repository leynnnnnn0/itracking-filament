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
use Filament\Tables\Columns\TextColumn;

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
                    ->options(Gender::values())
                    ->required(),

                DatePicker::make('date_of_birth')
                    ->required()
                    ->disabledDates(function () {
                        return collect(range(0, 365))
                            ->map(fn($day) => now()->addDays($day)->format('Y-m-d'))
                            ->toArray();
                    })
                    ->rule(['date', 'before:today'])
                    ->closeOnDateSelection()
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

                DatePicker::make('start_date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->required(),

                DatePicker::make('end_date')
                    ->native(false)
                    ->after('after:start_date')
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
