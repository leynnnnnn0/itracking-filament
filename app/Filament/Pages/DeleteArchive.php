<?php

namespace App\Filament\Pages;

use App\Filament\Resources\DashboardResource\Widgets\LowStockSuppy;
use App\Models\Position;
use App\Models\SoftDeleteRecord;
use App\Models\Unit;
use App\Models\User;
use Filament\Actions\RestoreAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\Concerns\InteractsWithRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class DeleteArchive extends Page implements HasTable
{
    use InteractsWithTable, InteractsWithRecords;

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'Sub Admin';
    }
    protected static ?string $navigationIcon = 'heroicon-o-trash';

    protected static string $view = 'filament.pages.delete-archive';

    protected function getHeaderWidgets(): array
    {
        return [
            LowStockSuppy::class
        ];
    }
    public function table(Table $table): Table
    {
        $userQuery = User::onlyTrashed()
            ->select(['id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'deleted_at', DB::raw("'User' as type")]);

        $unitQuery = Unit::onlyTrashed()
            ->select(['id', 'name', 'deleted_at', DB::raw("'Unit' as type")]);

        $positionQuery = Position::onlyTrashed()
            ->select(['id', 'name as name', 'deleted_at', DB::raw("'Position' as type")]); // Use 'name as name' to match

        return $table
            ->query(
                SoftDeleteRecord::query()
                    ->fromSub(
                        $userQuery->unionAll($unitQuery)->unionAll($positionQuery),
                        'soft_deleted_records'
                    )
                    ->orderBy('deleted_at', 'desc')
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Model Id'),
                TextColumn::make('name'),
                TextColumn::make('type'),
                TextColumn::make('deleted_at')
                    ->dateTime(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('view')
                    ->color('gray')
                    ->icon('heroicon-o-eye'),
                Action::make('Force delete')
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
                Action::make('restore')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-uturn-left')
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
