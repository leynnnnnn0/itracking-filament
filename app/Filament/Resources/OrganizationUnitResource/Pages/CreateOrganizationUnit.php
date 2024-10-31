<?php

namespace App\Filament\Resources\OrganizationUnitResource\Pages;

use App\Filament\Resources\OrganizationUnitResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganizationUnit extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = OrganizationUnitResource::class;
}
