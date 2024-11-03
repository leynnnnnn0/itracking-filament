<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateEquipment extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = EquipmentResource::class;
    protected static bool $canCreateAnother = false;
        
  
}
