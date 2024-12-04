<?php

namespace App\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Filament\Actions;

trait HasPdfDownload
{
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
