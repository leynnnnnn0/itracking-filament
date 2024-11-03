<?php

namespace App\Filament\Resources\AccountingOfficerResource\Pages;

use App\Filament\Resources\AccountingOfficerResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;

class CreateAccountingOfficer extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = AccountingOfficerResource::class;
    protected static bool $canCreateAnother = false;
}
