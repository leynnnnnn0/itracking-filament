<?php

namespace App\Filament\Resources\SupplyCategoryResource\Pages;

use App\Filament\Resources\SupplyCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplyCategories extends ListRecords
{
    protected static string $resource = SupplyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
