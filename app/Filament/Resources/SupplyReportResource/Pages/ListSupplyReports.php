<?php

namespace App\Filament\Resources\SupplyReportResource\Pages;

use App\Filament\Resources\SupplyReportResource;
use App\Traits\HasPdfDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplyReports extends ListRecords
{
    use HasPdfDownload;
    protected static string $resource = SupplyReportResource::class;

    protected function getViewName(): string
    {
        return 'supply-reports';
    }
}
