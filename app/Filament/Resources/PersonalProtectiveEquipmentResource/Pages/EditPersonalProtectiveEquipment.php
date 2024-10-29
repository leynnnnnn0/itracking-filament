<?php

namespace App\Filament\Resources\PersonalProtectiveEquipmentResource\Pages;

use App\Filament\Resources\PersonalProtectiveEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonalProtectiveEquipment extends EditRecord
{
    protected static string $resource = PersonalProtectiveEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
