<?php

namespace App\Filament\Resources\BorrowedEquipmentResource\Pages;

use App\Exports\BorrowedEquipmentExport;
use App\Filament\Resources\BorrowedEquipmentResource;
use App\Traits\HasDownloads;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;
use Maatwebsite\Excel\Facades\Excel;

class ListBorrowedEquipment extends ListRecords
{
    use HasDownloads;
    protected static string $resource = BorrowedEquipmentResource::class;
    protected static ?string $title = 'Borrow Equipment';

    public function export()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        $equipment = $query->get();
        return response()->streamDownload(
            function () use ($equipment) {
                echo Pdf::loadHtml(
                    Blade::render('pdf.borrowed-equipment-list', ['borrowedEquipments' => $equipment])
                )
                    ->setPaper('a3', 'landscape')
                    ->stream();
            },
            'borrowed-equipment-list.pdf'
        );
    }

    public function exportAsExcel()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        return Excel::download(new BorrowedEquipmentExport($query), 'borrowed-equipment.xlsx');
    }
}
