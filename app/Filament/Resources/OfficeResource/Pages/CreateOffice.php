<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOffice extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = OfficeResource::class;
}
