<?php

namespace App\Filament\Resources\OfficeAgencyResource\Pages;

use App\Filament\Resources\OfficeAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfficeAgencies extends ListRecords
{
    protected static string $resource = OfficeAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
