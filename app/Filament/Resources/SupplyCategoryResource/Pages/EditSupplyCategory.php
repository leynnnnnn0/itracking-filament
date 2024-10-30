<?php

namespace App\Filament\Resources\SupplyCategoryResource\Pages;

use App\Filament\Resources\SupplyCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplyCategory extends EditRecord
{
    protected static string $resource = SupplyCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
