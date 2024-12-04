<?php

namespace App\Filament\Resources\SupplyHistoryResource\Pages;

use App\Exports\SupplyHistoryExport;
use App\Filament\Resources\SupplyHistoryResource;
use App\Models\Supply;
use App\Models\SupplyHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ListSupplyHistories extends ListRecords
{
    protected static string $resource = SupplyHistoryResource::class;

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
        return Excel::download(new SupplyHistoryExport($query), 'supply-history.xlsx');
    }

    public function export()
    {
        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(300);

            $query = $this->getFilteredTableQuery();
            $this->applySortingToTableQuery($query);
            $filters = $this->tableFilters;

            $from = $filters['created_at']['created_from'];
            $until = $filters['created_at']['created_until'];

            $suppliesWithHistory = $query->pluck('supply_id')->unique();

            $suppliesWithoutHistory = Supply::whereNotIn('id', $suppliesWithHistory)->get();


            foreach ($suppliesWithoutHistory as $supply) {
                SupplyHistory::create([
                    'supply_id' => $supply->id,
                    'quantity' => $supply->quantity,
                    'missing' => $supply->missing,
                    'expired' => $supply->expired,
                    'used' => $supply->used,
                    'added' => 0,
                    'total' => $supply->total,
                ]);
            }


            if ($from && $until) {
                $query->monthlySummary($from, $until);
            }

            $supplies = $query->orderBy('created_at', 'desc')->get();

            return response()->streamDownload(
                function () use ($supplies, $from, $until) {
                    echo Pdf::loadHtml(
                        Blade::render('pdf.supply-history-list', [
                            'supplies' => $supplies,
                            'from' => $from,
                            'until' => $until
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
