<?php

namespace App\Filament\Resources\MissingEquipmentResource\Pages;

use App\Filament\Resources\MissingEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMissingEquipment extends ListRecords
{
    protected static string $resource = MissingEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
