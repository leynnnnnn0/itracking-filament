<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use App\Models\AccountableOfficer;
use App\Models\Personnel;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_as_pdf')
                ->color('gray')
                ->label('Export as PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn() => $this->export()),
            Actions\CreateAction::make(),
        ];
    }

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
}
