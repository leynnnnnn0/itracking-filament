<?php

namespace App\Filament\Resources\BorrowedEquipmentResource\Pages;

use App\Filament\Resources\BorrowedEquipmentResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;

class ListBorrowedEquipment extends ListRecords
{
    protected static string $resource = BorrowedEquipmentResource::class;

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
}
