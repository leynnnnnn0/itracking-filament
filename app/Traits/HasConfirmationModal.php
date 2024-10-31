<?php

namespace App\Traits;

use Filament\Actions;

trait HasConfirmationModal
{
    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                $this->create();
            });
    }
    
}
