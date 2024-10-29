<?php

namespace App\Filament\Resources\MissingEquipmentResource\Pages;

use App\Enum\EquipmentStatus;
use App\Filament\Resources\MissingEquipmentResource;
use App\Traits\HasRedirectUrl;
use Exception;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMissingEquipment extends CreateRecord
{
    use HasRedirectUrl;
    protected static string $resource = MissingEquipmentResource::class;

    protected function afterCreate()
    {
        try {
            $missingEquipment = $this->record;
            $equipment = $missingEquipment->equipment;

            $status = $equipment->status;
            $totalAvaibleEquipment = $equipment->quantity_available - $missingEquipment->quantity;
            if ($missingEquipment->is_condemned) {
                $totalCondemnedEquipment = $equipment->total_quantity_condemned;
                $equipment->quantity_condemned = $totalCondemnedEquipment;
            } else {
                $totalMissingEquipment = $equipment->quantity_missing + $missingEquipment->quantity;
                $equipment->quantity_missing = $totalMissingEquipment;
            }
            $equipment->quantity_available = $totalAvaibleEquipment;
            $equipment->status = $status;
            $equipment->save();
        } catch (Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->success()
                ->send();
        }
    }
}
