<?php

namespace App\Filament\Resources\SupplyReportResource\Pages;

use App\Exports\SupplyReportExport;
use App\Filament\Resources\SupplyReportResource;
use App\Traits\HasDownloads;
use App\Traits\HasPdfDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListSupplyReports extends ListRecords
{
    use HasPdfDownload, HasDownloads;
    protected static string $resource = SupplyReportResource::class;

    public function exportAsExcel()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        return Excel::download(new SupplyReportExport($query), 'supply-report.xlsx');
    }

    protected function getViewName(): string
    {
        return 'supply-reports';
    }
}
