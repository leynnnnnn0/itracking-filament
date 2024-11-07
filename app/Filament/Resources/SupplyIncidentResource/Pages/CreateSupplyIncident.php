<?php

namespace App\Filament\Resources\SupplyIncidentResource\Pages;

use App\Filament\Resources\SupplyIncidentResource;
use App\Models\Supply;
use App\Traits\HasAuthorizationCheck;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateSupplyIncident extends CreateRecord
{
    use HasAuthorizationCheck, HasConfirmationModal, HasRedirectUrl;
    protected static string $resource = SupplyIncidentResource::class;
    protected static bool $canCreateAnother = false;

    public function afterCreate()
    {
        $supply = Supply::find($this->record->supply_id);
        $supply->total -= $this->record->quantity;
        if ($this->record->type === 'missing') {
            $supply->missing += $this->record->quantity;
        } else {
            $supply->expired += $this->record->quantity;
        }
        $supply->save();
    }
}
