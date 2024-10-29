<?php

namespace App\Filament\Resources\AccountingOfficerResource\Pages;

use App\Filament\Resources\AccountingOfficerResource;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAccountingOfficer extends CreateRecord
{
    use HasRedirectUrl;
    protected static string $resource = AccountingOfficerResource::class;
}
