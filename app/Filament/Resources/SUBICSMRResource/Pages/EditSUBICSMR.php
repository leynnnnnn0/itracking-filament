<?php

namespace App\Filament\Resources\SUBICSMRResource\Pages;

use App\Filament\Resources\SUBICSMRResource;
use App\Traits\HasRedirectUrl;
use App\Traits\HasUpdateConfirmationModal;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSUBICSMR extends EditRecord
{
    use HasRedirectUrl, HasUpdateConfirmationModal;
    protected static string $resource = SUBICSMRResource::class;
}
