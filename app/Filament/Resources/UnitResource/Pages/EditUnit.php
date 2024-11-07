<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use App\Traits\HasRedirectUrl;
use App\Traits\HasUpdateConfirmationModal;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnit extends EditRecord
{
    use HasRedirectUrl, HasUpdateConfirmationModal;
    protected static string $resource = UnitResource::class;

}
