<?php

namespace App\Filament\Resources\SupplyHistoryResource\Pages;

use App\Filament\Resources\SupplyHistoryResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;
use Throwable;

class ListSupplyHistories extends ListRecords
{
    protected static string $resource = SupplyHistoryResource::class;

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
        try {
            ini_set('memory_limit', '1024M');  // Increased to 4GB
            set_time_limit(300);  // Add 5 minutes timeout just in case

            $query = $this->getFilteredTableQuery();
            $this->applySortingToTableQuery($query);
            $filters = $this->tableFilters;

            $supplies = $query->get();
            return response()->streamDownload(
                function () use ($supplies, $filters) {
                    echo Pdf::loadHtml(
                        Blade::render('pdf.supply-history-list', [
                            'supplies' => $supplies,
                            'from' => $filters['created_at']['created_from'],
                            'until' => $filters['created_at']['created_until']
                        ])
                    )
                        ->setPaper('a3', 'landscape')
                        ->stream();
                },
                'supply-history-list.pdf'
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
}
