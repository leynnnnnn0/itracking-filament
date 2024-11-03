<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = UserResource::class;

    protected static bool $canCreateAnother = false;
    
}
