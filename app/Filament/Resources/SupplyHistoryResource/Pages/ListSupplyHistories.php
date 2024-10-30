<?php

namespace App\Filament\Resources\SupplyHistoryResource\Pages;

use App\Filament\Resources\SupplyHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplyHistories extends ListRecords
{
    protected static string $resource = SupplyHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
