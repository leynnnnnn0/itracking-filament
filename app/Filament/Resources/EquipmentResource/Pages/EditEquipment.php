<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use App\Models\EquipmentHistory;
use App\Traits\HasModelStatusIdentifier;
use App\Traits\HasRedirectUrl;
use App\Traits\HasUpdateConfirmationModal;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Actions\Action as NotifAction;
use Illuminate\Support\Facades\DB;

class EditEquipment extends EditRecord
{
    use HasRedirectUrl, HasUpdateConfirmationModal, HasModelStatusIdentifier;
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                $record = $this->record;
                $data = $this->form->getState();
                $previousQuantity = $this->data['previous_quantity'];
                $newQuantity = $this->data['quantity'];
                
                $totalQuantity =  $record->quantity_missing + $record->quantity_borrowed + $record->quantity_condemned;
                if ($newQuantity < $totalQuantity) {
                    $this->closeActionModal();
                    Notification::make()
                        ->title('Invalid Quantity')
                        ->body("New quantity ({$newQuantity}) must be at least equal to the total quantity in use ($totalQuantity)")
                        ->danger()
                        ->send();

                    $this->halt();
                } else if ($previousQuantity !== $newQuantity)
                    $data['quantity_available'] += $newQuantity - $previousQuantity;


                $this->form->fill($data);
                $this->record->status = self::getEquimentStatus($this->record);

                $this->save();
            });
    }


    protected function afterSave(): void
    {
        // Access the form data
        $equipment = $this->record;
        $previousPersonnel = $this->data['previous_personnel'];
        $previousAccountableOfficer = $this->data['previous_accountable_officer'];
        $newPersonnel = $equipment->personnel->full_name;

        $newAccountableOfficer = $equipment->accountable_officer->full_name;


        // Check if personnel changed
        if ($previousPersonnel != $newPersonnel || $previousAccountableOfficer != $newAccountableOfficer) {
            Notification::make()
                ->title('Download PDF')
                ->success()
                ->body('Equipment details changed.')
                ->actions([
                    NotifAction::make('download')
                        ->url(route('equipment-pdf', [$equipment, $previousPersonnel, $previousAccountableOfficer]), true)
                ])
                ->duration(100000)
                ->send();
        }
    }
}
