<?php

namespace App\Filament\Resources\SupplyReportResource\Pages;

use App\Filament\Resources\SupplyReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplyReports extends ListRecords
{
    protected static string $resource = SupplyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
