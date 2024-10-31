<?php

namespace App\Filament\Resources\OperatingUnitProjectResource\Pages;

use App\Filament\Resources\OperatingUnitProjectResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOperatingUnitProject extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = OperatingUnitProjectResource::class;
}
