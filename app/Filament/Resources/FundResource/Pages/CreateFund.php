<?php

namespace App\Filament\Resources\FundResource\Pages;

use App\Filament\Resources\FundResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFund extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = FundResource::class;
    protected static bool $canCreateAnother = false;
}
