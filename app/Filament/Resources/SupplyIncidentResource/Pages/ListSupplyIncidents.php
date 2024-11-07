<?php

namespace App\Filament\Resources\SupplyIncidentResource\Pages;

use App\Filament\Resources\SupplyIncidentResource;
use App\Traits\HasPdfDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplyIncidents extends ListRecords
{
    use HasPdfDownload;
    protected static string $resource = SupplyIncidentResource::class;

    protected function getViewName(): string
    {
        return 'supply-incidents-list';
    }
}
