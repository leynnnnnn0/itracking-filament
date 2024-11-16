<?php

namespace App\Filament\Resources\OfficeAgencyResource\Pages;

use App\Filament\Resources\OfficeAgencyResource;
use App\Traits\HasRedirectUrl;
use App\Traits\HasUpdateConfirmationModal;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOfficeAgency extends EditRecord
{
    use HasRedirectUrl, HasUpdateConfirmationModal;
    protected static string $resource = OfficeAgencyResource::class;
}
