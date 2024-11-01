<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Filament\Resources\SupplyResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;

class ListSupplies extends ListRecords
{
    protected static string $resource = SupplyResource::class;

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
        $supplies = $query->get();
        return response()->streamDownload(
            function () use ($supplies) {
                echo Pdf::loadHtml(
                    Blade::render('pdf.supplies-list', ['supplies' => $supplies])
                )
                    ->setPaper('a3', 'landscape')
                    ->stream();
            },
            'supplies-list.pdf'
        );
    }
}
