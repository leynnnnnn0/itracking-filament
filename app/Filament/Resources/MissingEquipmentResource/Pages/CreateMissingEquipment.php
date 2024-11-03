<?php

namespace App\Filament\Resources\MissingEquipmentResource\Pages;

use App\Enum\EquipmentStatus;
use App\Filament\Resources\MissingEquipmentResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasModelStatusIdentifier;
use App\Traits\HasRedirectUrl;
use Exception;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateMissingEquipment extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal, HasModelStatusIdentifier;
    protected static string $resource = MissingEquipmentResource::class;
    protected static bool $canCreateAnother = false;

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                try {
                    DB::transaction(function () {
                        $this->create();

                        $missingEquipment = $this->record;
                        $equipment = $missingEquipment->equipment;

                        $totalAvaibleEquipment = $equipment->quantity_available - $missingEquipment->quantity;
                        if ($missingEquipment->is_condemned) {
                            $totalCondemnedEquipment = $equipment->total_quantity_condemned + $missingEquipment->quantity;
                            $equipment->quantity_condemned = $totalCondemnedEquipment;
                        } else {
                            $totalMissingEquipment = $equipment->quantity_missing + $missingEquipment->quantity;
                            $equipment->quantity_missing = $totalMissingEquipment;
                        }
                        $equipment->quantity_available = $totalAvaibleEquipment;
                        $equipment->status = self::getEquimentStatus($equipment);
                        $equipment->save();
                    });
                } catch (Exception $e) {
                    Notification::make()
                        ->title('Error')
                        ->body($e->getMessage())
                        ->success()
                        ->send();
                }
            });
    }
    
}
