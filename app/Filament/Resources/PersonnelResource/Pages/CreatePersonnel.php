<?php

namespace App\Filament\Resources\PersonnelResource\Pages;

use App\Filament\Resources\PersonnelResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePersonnel extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = PersonnelResource::class;
}
