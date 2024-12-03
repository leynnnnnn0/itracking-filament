<?php

namespace App\Filament\Resources\PersonnelResource\Pages;

use App\Exports\PersonnelExport;
use App\Filament\Resources\PersonnelResource;
use App\Traits\HasExcelDownload;
use App\Traits\HasPdfDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListPersonnels extends ListRecords
{
    use HasPdfDownload;
    protected static string $resource = PersonnelResource::class;
    protected static ?string $title = 'Personnel';

    protected function getViewName(): string
    {
        return 'personnel-list';
    }

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
        return Excel::download(new PersonnelExport($query), 'personnel.xlsx');
    }
}
