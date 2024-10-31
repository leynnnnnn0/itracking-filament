<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditResource\Pages;
use App\Filament\Resources\AuditResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Str;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

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
                TextColumn::make('id'),
                TextColumn::make('user.full_name'),
                TextColumn::make('event')
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                TextColumn::make('auditable_type')
                    ->formatStateUsing(fn($state) => Str::title(Str::snake(Str::after($state, 'Models\\'), ' '))),
                TextColumn::make('auditable_id'),
                TextColumn::make('created_at')
                    ->date('F d, Y'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
                TextEntry::make('user.full_name'),

                TextEntry::make('auditable_type')
                    ->label('Auditable Type')
                    ->formatStateUsing(fn($state) => Str::title(Str::snake(Str::afterLast($state, '\\'), ' '))),

                KeyValueEntry::make('old_values'),

                KeyValueEntry::make('new_values'),

                TextEntry::make('ip_address')
                    ->label('IP Address'),

                TextEntry::make('user_agent')
                    ->label('User Agent')
                    ->limit(50)
                    ->tooltip(function (TextEntry $component): ?string {
                        return $component->getState();
                    }),

                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime(),
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
            'index' => Pages\ListAudits::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user'])->latest();
    }
}
