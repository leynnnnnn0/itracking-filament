<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SUBICSMRResource\Pages;
use App\Filament\Resources\SUBICSMRResource\RelationManagers;
use App\Models\SubICSMFR;
use App\Models\SUBICSMR;
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

class SUBICSMRResource extends Resource
{
    protected static ?string $model = SubICSMFR::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Sub ICS/MR';
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Details')
                    ->schema([
                        TextInput::make('name')
                            ->unique()
                            ->rules([
                                'string',
                                'regex:/[a-zA-Z]/',
                            ])->required()
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
                    ->visible(condition: Auth::user()->role === 'Admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Model $record) {
                        // Check if there are associated personnel
                        if ($record->personnel()->exists()) {
                            Notification::make()
                                ->title('Deletion Failed')
                                ->body('Cannot delete this sub ics/mr because it has associated personnel.')
                                ->danger()
                                ->send();

                            return false; // Prevent deletion
                        }

                        // If no associated personnel, proceed with deletion
                        $record->delete(); // Delete the record
                    })
                    ->label('Archive')
                    ->requiresConfirmation()
                    ->modalIconColor('danger')
                    ->color('danger')
                    ->modalHeading('Delete sub ics/mr')
                    ->modalDescription('Are you sure you\'d like to delete this sub ics/mr?')
                    ->modalSubmitActionLabel('Yes, Delete it'),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Archive')
                        ->modalHeading('Archive sub ics/mrs')
                        ->successNotificationTitle('Archived'),
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
            'index' => Pages\ListSUBICSMRS::route('/'),
            'create' => Pages\CreateSUBICSMR::route('/create'),
            'edit' => Pages\EditSUBICSMR::route('/{record}/edit'),
        ];
    }
}
