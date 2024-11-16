<?php

namespace App\Filament\Resources\OfficeAgencyResource\Pages;

use App\Filament\Resources\OfficeAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOfficeAgency extends EditRecord
{
    protected static string $resource = OfficeAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
