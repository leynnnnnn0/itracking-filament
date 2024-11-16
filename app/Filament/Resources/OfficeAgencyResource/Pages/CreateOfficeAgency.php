<?php

namespace App\Filament\Resources\OfficeAgencyResource\Pages;

use App\Filament\Resources\OfficeAgencyResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOfficeAgency extends CreateRecord
{
    use HasConfirmationModal, HasRedirectUrl;
    protected static string $resource = OfficeAgencyResource::class;
    protected static bool $canCreateAnother = false;
}

