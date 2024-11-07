<?php

namespace App\Filament\Resources\FundResource\Pages;

use App\Filament\Resources\FundResource;
use App\Traits\HasRedirectUrl;
use App\Traits\HasUpdateConfirmationModal;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFund extends EditRecord
{
    use HasRedirectUrl, HasUpdateConfirmationModal;
    protected static string $resource = FundResource::class;


}
