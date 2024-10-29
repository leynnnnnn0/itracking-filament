<?php

namespace App\Filament\Resources\MissingEquipmentResource\Pages;

use App\Filament\Resources\MissingEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMissingEquipment extends EditRecord
{
    protected static string $resource = MissingEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
