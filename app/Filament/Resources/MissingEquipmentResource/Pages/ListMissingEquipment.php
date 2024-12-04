<?php

namespace App\Filament\Resources\MissingEquipmentResource\Pages;

use App\Exports\MissingEquipmentExport;
use App\Filament\Resources\MissingEquipmentResource;
use App\Traits\HasDownloads;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;
use Maatwebsite\Excel\Facades\Excel;

class ListMissingEquipment extends ListRecords
{
    use HasDownloads;
    protected static string $resource = MissingEquipmentResource::class;
    protected static ?string $title = 'Missing Equipment';

    public function export()
    {
        $query = $this->getFilteredTableQuery()->with(['equipment.personnel', 'equipment.accountable_officer']);
        $this->applySortingToTableQuery($query);
        $reports = $query->get();
        return response()->streamDownload(
            function () use ($reports) {
                echo Pdf::loadHtml(
                    Blade::render('pdf.missing-equipment-list', ['reports' => $reports])
                )
                    ->setPaper('a3', 'landscape')
                    ->stream();
            },
            'missing-equipment-list.pdf'
        );
    }

    public function exportAsExcel()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        return Excel::download(new MissingEquipmentExport($query), 'missing-equipment.xlsx');
    }
}
