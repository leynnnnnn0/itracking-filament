<?php

namespace App\Filament\Resources\OperatingUnitProjectResource\Pages;

use App\Filament\Resources\OperatingUnitProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOperatingUnitProjects extends ListRecords
{
    protected static string $resource = OperatingUnitProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
