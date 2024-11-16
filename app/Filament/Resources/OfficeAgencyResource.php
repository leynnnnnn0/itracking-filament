<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeAgencyResource\Pages;
use App\Filament\Resources\OfficeAgencyResource\RelationManagers;
use App\Models\OfficeAgency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfficeAgencyResource extends Resource
{
    protected static ?string $model = OfficeAgency::class;
    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListOfficeAgencies::route('/'),
            'create' => Pages\CreateOfficeAgency::route('/create'),
            'edit' => Pages\EditOfficeAgency::route('/{record}/edit'),
        ];
    }
}
