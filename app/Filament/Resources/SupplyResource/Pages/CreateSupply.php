<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Filament\Resources\SupplyResource;
use App\Models\MissingEquipment;
use App\Models\SupplyHistory;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Exception;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateSupply extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = SupplyResource::class;
    protected static bool $canCreateAnother = false;

}
