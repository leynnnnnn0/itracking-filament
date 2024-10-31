<?php

namespace App\Traits;

use Filament\Actions;

trait HasUpdateConfirmationModal
{
    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                $this->save();
            });
    }
}
