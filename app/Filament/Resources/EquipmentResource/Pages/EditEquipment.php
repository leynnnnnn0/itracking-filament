<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use App\Traits\HasRedirectUrl;
use App\Traits\HasUpdateConfirmationModal;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Actions\Action as NotifAction;


class EditEquipment extends EditRecord
{
    use HasRedirectUrl, HasUpdateConfirmationModal;
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }


    protected function afterSave(): void
    {
        // Access the form data
        $equipment = $this->record;
        $previousPersonnel = $this->data['previous_personnel'];
        $newPersonnel = $equipment->personnel->full_name;

        // Check if personnel changed
        if ($previousPersonnel != $newPersonnel) {
            Notification::make()
                ->title('Download PDF')
                ->success()
                ->body('Equipment Responsible Person Changed.')
                ->actions([
                    NotifAction::make('download')
                        ->url(route('equipment-pdf', [$equipment, $previousPersonnel]), true)
                ])
                ->duration(100000)
                ->send();
        }
    }
}
