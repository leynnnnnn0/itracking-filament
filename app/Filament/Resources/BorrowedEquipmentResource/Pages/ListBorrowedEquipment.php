<?php

namespace App\Filament\Resources\BorrowedEquipmentResource\Pages;

use App\Filament\Resources\BorrowedEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBorrowedEquipment extends ListRecords
{
    protected static string $resource = BorrowedEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
