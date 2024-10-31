<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationUnitResource\Pages;
use App\Filament\Resources\OrganizationUnitResource\RelationManagers;
use App\Models\OrganizationUnit;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizationUnitResource extends Resource
{
    protected static ?string $model = OrganizationUnit::class;
    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Organization Unit Details')
                    ->schema([
                        TextInput::make('name') ->maxLength(30)->required()
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Model $record) {
                        // Check if there are associated personnel
                        if ($record->equipment()->exists()) {
                            Notification::make()
                                ->title('Deletion Failed')
                                ->body('Cannot delete this organization unit because it has associated equipment.')
                                ->danger()
                                ->send();

                            return false; // Prevent deletion
                        }

                        // If no associated personnel, proceed with deletion
                        $record->delete(); // Delete the record
                    })
                    ->requiresConfirmation()
                    ->modalIconColor('danger')
                    ->color('danger')
                    ->modalHeading('Delete Organization Unit')
                    ->modalDescription('Are you sure you\'d like to delete this organization unit?')
                    ->modalSubmitActionLabel('Yes, Delete it')
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
            'index' => Pages\ListOrganizationUnits::route('/'),
            'create' => Pages\CreateOrganizationUnit::route('/create'),
            'edit' => Pages\EditOrganizationUnit::route('/{record}/edit'),
        ];
    }
}
