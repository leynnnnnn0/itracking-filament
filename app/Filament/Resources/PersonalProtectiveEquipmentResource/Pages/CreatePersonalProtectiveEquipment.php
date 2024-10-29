<?php

namespace App\Filament\Resources\PersonalProtectiveEquipmentResource\Pages;

use App\Filament\Resources\PersonalProtectiveEquipmentResource;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePersonalProtectiveEquipment extends CreateRecord
{
    use HasRedirectUrl;
    protected static string $resource = PersonalProtectiveEquipmentResource::class;
}
