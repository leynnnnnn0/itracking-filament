<?php

namespace App\Filament\Resources\SupplyIncidentResource\Pages;

use App\Exports\SupplyIncidentExport;
use App\Filament\Resources\SupplyIncidentResource;
use App\Traits\HasPdfDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListSupplyIncidents extends ListRecords
{
    use HasPdfDownload;
    protected static string $resource = SupplyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_as_excel')
                ->color('gray')
                ->label('Export as Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn() => $this->exportAsExcel()),
            Actions\Action::make('export_as_pdf')
                ->color('gray')
                ->label('Export as PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn() => $this->export()),
            Actions\CreateAction::make(),
        ];
    }

    public function exportAsExcel()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        return Excel::download(new SupplyIncidentExport($query), 'supply-incident.xlsx');
    }

    protected function getViewName(): string
    {
        return 'supply-incidents-list';
    }
}
