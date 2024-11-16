<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeAgencyResource\Pages;
use App\Filament\Resources\OfficeAgencyResource\RelationManagers;
use App\Models\OfficeAgency;
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
use Illuminate\Support\Facades\Auth;

class OfficeAgencyResource extends Resource
{
    protected static ?string $model = OfficeAgency::class;
    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Unit Details')
                    ->schema([
                        TextInput::make('name')
                            ->unique()
                            ->rules([
                                'string',
                                'regex:/[a-zA-Z]/',
                            ])->maxLength(30)->required()
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
                Tables\Filters\TrashedFilter::make()
                    ->visible(Auth::user()->role === 'Admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => !$record->deleted_at),
                Tables\Actions\DeleteAction::make()
                    ->label('Archive')
                    ->modalHeading('Archive Office/Agency')
                    ->modalDescription('Are you sure you\'d like to archive this office/agency?')
                    ->modalSubmitActionLabel('Yes, Archive it'),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalHeading('Archive Office/Agency')
                        ->modalDescription('Are you sure you\'d like to archive these office/agency?')
                        ->modalSubmitActionLabel('Yes, Archive it'),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListOfficeAgencies::route('/'),
            'create' => Pages\CreateOfficeAgency::route('/create'),
            'edit' => Pages\EditOfficeAgency::route('/{record}/edit'),
        ];
    }
}
