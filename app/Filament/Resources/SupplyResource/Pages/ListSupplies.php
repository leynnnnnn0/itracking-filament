<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Exports\SupplyExport;
use App\Filament\Resources\SupplyResource;
use App\Traits\HasDownloads;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Throwable;

class ListSupplies extends ListRecords
{
    use HasDownloads;
    protected static string $resource = SupplyResource::class;

    public function export()
    {
        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(300);

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
        } catch (Throwable $e) {
            Notification::make()
                ->title('Export Error')
                ->body('An error occurred while generating the PDF. Please try refining your filters or limiting the data selection to prevent issues with file size or memory limits.')
                ->danger()
                ->send();

            return back();
        }
    }

    public function exportAsExcel()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        return Excel::download(new SupplyExport($query), 'supply.xlsx');
    }
}
