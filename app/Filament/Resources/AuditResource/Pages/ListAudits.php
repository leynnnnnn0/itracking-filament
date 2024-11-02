<?php

namespace App\Filament\Resources\AuditResource\Pages;

use App\Filament\Resources\AuditResource;
use App\Traits\HasPdfDownload;
use Filament\Resources\Pages\ListRecords;

class ListAudits extends ListRecords
{
    use HasPdfDownload;
    protected static string $resource = AuditResource::class;

    protected function getViewName(): string
    {
        return 'audits-list';
    }
}
