<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoftDeletedItemsResource\Pages;
use App\Models\Equipment;
use App\Models\Supply;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SoftDeletedItemsResource extends Panel
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // No specific model set, we handle multiple models through queries
    public static function getEloquentQuery(): Builder
    {
        $users = User::onlyTrashed()->select(['id', 'first_name AS name', 'deleted_at', DB::raw("'User' AS model_type")]);
        $equipments = Equipment::onlyTrashed()->select(['id', 'name', 'deleted_at', DB::raw("'Equipment' AS model_type")]);
        $supplies = Supply::onlyTrashed()->select(['id', 'description AS name', 'deleted_at', DB::raw("'Supply' AS model_type")]);

        return $users->union($equipments)->union($supplies);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Define form fields if necessary (optional for viewing deleted items)
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSoftDeletedItems::route('/'),
            // Remove create and edit if not applicable
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
