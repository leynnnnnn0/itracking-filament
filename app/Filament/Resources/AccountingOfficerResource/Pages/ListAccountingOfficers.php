<?php

namespace App\Filament\Resources\AccountingOfficerResource\Pages;

use App\Filament\Resources\AccountingOfficerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccountingOfficers extends ListRecords
{
    protected static string $resource = AccountingOfficerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
