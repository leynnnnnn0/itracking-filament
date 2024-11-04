<?php

namespace App\Traits;

use Exception;
use Filament\Actions;
use Filament\Notifications\Notification;

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
