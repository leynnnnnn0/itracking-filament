<?php

namespace App\Filament\Resources\PositionResource\Pages;

use App\Filament\Resources\PositionResource;
use App\Traits\HasRedirectUrl;
use App\Traits\HasUpdateConfirmationModal;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPosition extends EditRecord
{
    use HasRedirectUrl, HasUpdateConfirmationModal;
    protected static string $resource = PositionResource::class;


}
