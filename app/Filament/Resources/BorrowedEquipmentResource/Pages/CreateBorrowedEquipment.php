<?php

namespace App\Filament\Resources\BorrowedEquipmentResource\Pages;

use App\BorrowStatus;
use App\Enum\EquipmentStatus;
use App\Filament\Resources\BorrowedEquipmentResource;
use App\Models\Equipment;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Exception;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBorrowedEquipment extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = BorrowedEquipmentResource::class;
    protected static bool $canCreateAnother = false;
    protected static ?string $title = 'Borrow Equipment';

    protected function afterCreate()
    {
        try {
            $borrowedEquipment = $this->record;
            $equipment = $borrowedEquipment->equipment;

            $status = $equipment->status;
            $totalBorrowedEquipment = $equipment->quantity_borrowed;
            $totalAvailableEquipment = $equipment->quantity_available;
            $totalMissingQuantity = $equipment->quantity_missing;
            if ($borrowedEquipment->status === BorrowStatus::BORROWED->value) {
                $totalAvailableEquipment -= $borrowedEquipment->quantity;
                $totalBorrowedEquipment += $borrowedEquipment->quantity;

                if ($totalBorrowedEquipment === $equipment->quantity_available) {
                    $status = EquipmentStatus::FULLY_BORROWED->value;
                } else {
                    $status =  EquipmentStatus::PARTIALLY_BORROWED->value;
                }
            }

            $equipment->update([
                'status' => $status,
                'quantity_available' => $totalAvailableEquipment,
                'quantity_borrowed' => $totalBorrowedEquipment,
                'quantity_missing' => $totalMissingQuantity,
            ]);
        } catch (Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->success()
                ->send();
        }
    }
}
