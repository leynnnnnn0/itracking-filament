<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Exports\UsersExport;
use App\Filament\Resources\UserResource;
use App\Traits\HasExcelDownload;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;
use Maatwebsite\Excel\Facades\Excel;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
        return Excel::download(new UsersExport($query), 'users.xlsx');
    }

    public function export()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        $users = $query->get();
        return response()->streamDownload(
            function () use ($users) {
                echo Pdf::loadHtml(
                    Blade::render('pdf.users-list', ['users' => $users])
                )
                    ->setPaper('a3', 'landscape')
                    ->stream();
            },
            'users-list.pdf'
        );
    }
}
