<?php

namespace App\Traits;

use Filament\Actions;

trait HasDownloads
{
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
}
