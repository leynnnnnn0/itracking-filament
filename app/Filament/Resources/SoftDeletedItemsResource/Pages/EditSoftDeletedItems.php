<?php

namespace App\Filament\Resources\SoftDeletedItemsResource\Pages;

use App\Filament\Resources\SoftDeletedItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoftDeletedItems extends EditRecord
{
    protected static string $resource = SoftDeletedItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
