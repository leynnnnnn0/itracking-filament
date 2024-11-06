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

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();

                DB::transaction(function () {
                    $supply = Supply::findOrFail($this->data['supply_id']);
                    $supply->total -= $this->data['quantity'];
                    $supply->save();
                    $this->create();
                });
            });
    }
}
