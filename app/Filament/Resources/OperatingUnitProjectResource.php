<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperatingUnitProjectResource\Pages;
use App\Filament\Resources\OperatingUnitProjectResource\RelationManagers;
use App\Models\OperatingUnitProject;
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
use Illuminate\Support\Facades\Auth;

class OperatingUnitProjectResource extends Resource
{
    protected static ?string $model = OperatingUnitProject::class;
    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Operating Unit/Project Details')
                    ->schema([
                        TextInput::make('name')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Model $record) {
                        // Check if there are associated personnel
                        if ($record->equipment()->exists()) {
                            Notification::make()
                                ->title('Deletion Failed')
                                ->body('Cannot delete this operating unit/project because it has associated equipment.')
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
                    ->modalHeading('Delete operating unit/project')
                    ->modalDescription('Are you sure you\'d like to delete this operating unit/project?')
                    ->modalSubmitActionLabel('Yes, Delete it'),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOperatingUnitProjects::route('/'),
            'create' => Pages\CreateOperatingUnitProject::route('/create'),
            'edit' => Pages\EditOperatingUnitProject::route('/{record}/edit'),
        ];
    }
}
