<?php

namespace App\Filament\Resources\AccountingOfficerResource\Pages;

use App\Filament\Resources\AccountingOfficerResource;
use App\Traits\HasPdfDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccountingOfficers extends ListRecords
{
    use HasPdfDownload;
    protected static string $resource = AccountingOfficerResource::class;

    protected function getViewName(): string
    {
        return 'accountable-officers-list';
    }
}
