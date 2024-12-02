<?php

namespace App\Filament\Resources\SUBICSMRResource\Pages;

use App\Filament\Resources\SUBICSMRResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSUBICSMR extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = SUBICSMRResource::class;
    protected static ?string $title = 'Create Sub ICS/MR';

    protected static bool $canCreateAnother = false;
}
