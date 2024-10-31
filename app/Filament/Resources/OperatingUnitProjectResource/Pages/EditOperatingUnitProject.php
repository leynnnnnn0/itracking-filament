<?php

namespace App\Filament\Resources\OperatingUnitProjectResource\Pages;

use App\Filament\Resources\OperatingUnitProjectResource;
use App\Traits\HasRedirectUrl;
use App\Traits\HasUpdateConfirmationModal;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOperatingUnitProject extends EditRecord
{
    use HasRedirectUrl, HasUpdateConfirmationModal;
    protected static string $resource = OperatingUnitProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
