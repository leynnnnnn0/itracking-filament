<?php

namespace App\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Filament\Actions;

trait HasPdfDownload
{
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

    abstract protected function getViewName(): string;
    public function export()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        $model = $query->get();
        return response()->streamDownload(
            function () use ($model) {
                echo Pdf::loadHtml(
                    Blade::render('pdf.' . $this->getViewName(), ['model' => $model])
                )
                    ->setPaper('a3', 'landscape')
                    ->stream();
            },
            $this->getViewName() . '.pdf'
        );
    }
}
