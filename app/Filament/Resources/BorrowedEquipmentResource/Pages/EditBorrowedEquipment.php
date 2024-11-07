<?php

namespace App\Filament\Resources\BorrowedEquipmentResource\Pages;

use App\Filament\Resources\BorrowedEquipmentResource;
use App\Traits\HasRedirectUrl;
use App\Traits\HasUpdateConfirmationModal;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBorrowedEquipment extends EditRecord
{
    use HasRedirectUrl, HasUpdateConfirmationModal;
    protected static string $resource = BorrowedEquipmentResource::class;

   
}
