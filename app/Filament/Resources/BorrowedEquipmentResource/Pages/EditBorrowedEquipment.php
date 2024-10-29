<?php

namespace App\Filament\Resources\BorrowedEquipmentResource\Pages;

use App\Filament\Resources\BorrowedEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBorrowedEquipment extends EditRecord
{
    protected static string $resource = BorrowedEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
