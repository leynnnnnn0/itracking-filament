<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Exports\EquipmentExport;
use App\Filament\Resources\EquipmentResource;
use App\Models\AccountableOfficer;
use App\Models\Personnel;
use App\Traits\HasDownloads;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ListEquipment extends ListRecords
{
    use HasDownloads;
    protected static string $resource = EquipmentResource::class;


    public function export()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        $equipment = $query->get();
        $filters = $this->tableFilters;

        $responsiblePerson = false;
        $accountablePerson = false;

        if ($filters['accountable_officer']['value']) {
            $accountablePerson = AccountableOfficer::find($filters['accountable_officer']['value'])?->full_name ?? 'N/a';
        } else if ($filters['responsible_person']['value']) {
            $responsiblePerson = Personnel::find($filters['responsible_person']['value'])?->full_name ?? 'N/a';
        }

        return response()->streamDownload(
            function () use ($equipment, $responsiblePerson, $accountablePerson) {
                echo Pdf::loadHtml(
                    Blade::render('pdf.equipment-list', [
                        'equipments' => $equipment,
                        'responsiblePerson' => $responsiblePerson,
                        'accountablePerson' => $accountablePerson,
                    ])
                )
                    ->setPaper('a3', 'landscape')
                    ->stream();
            },
            'equipment-list.pdf'
        );
    }

    public function exportAsExcel()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        return Excel::download(new EquipmentExport($query), 'equipment.xlsx');
    }
}
