<?php

namespace App\Filament\Resources\MissingEquipmentResource\Pages;

use App\Filament\Resources\MissingEquipmentResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;

class ListMissingEquipment extends ListRecords
{
    protected static string $resource = MissingEquipmentResource::class;

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
}
