<?php

namespace App\Filament\Resources\SupplyIncidentResource\Pages;

use App\Filament\Resources\SupplyIncidentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplyIncidents extends ListRecords
{
    protected static string $resource = SupplyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
