<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUnit extends CreateRecord
{
    use HasConfirmationModal, HasRedirectUrl;
    protected static string $resource = UnitResource::class;
    protected static bool $canCreateAnother = false;
}
