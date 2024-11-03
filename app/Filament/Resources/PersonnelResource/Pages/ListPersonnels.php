<?php

namespace App\Filament\Resources\PersonnelResource\Pages;

use App\Filament\Resources\PersonnelResource;
use App\Traits\HasPdfDownload;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonnels extends ListRecords
{
    use HasPdfDownload;
    protected static string $resource = PersonnelResource::class;

    protected function getViewName(): string
    {
        return 'personnel-list';
    }
}
