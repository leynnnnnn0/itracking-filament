<?php

namespace App\Filament\Resources\OrganizationUnitResource\Pages;

use App\Filament\Resources\OrganizationUnitResource;
use App\Traits\HasRedirectUrl;
use App\Traits\HasUpdateConfirmationModal;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationUnit extends EditRecord
{
    use HasRedirectUrl, HasUpdateConfirmationModal;
    protected static string $resource = OrganizationUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
