<?php

namespace App\Filament\Resources\PersonalProtectiveEquipmentResource\Pages;

use App\Filament\Resources\PersonalProtectiveEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonalProtectiveEquipment extends ListRecords
{
    protected static string $resource = PersonalProtectiveEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
