<?php

namespace App\Filament\Resources\OperatingUnitProjectResource\Pages;

use App\Filament\Resources\OperatingUnitProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOperatingUnitProject extends EditRecord
{
    protected static string $resource = OperatingUnitProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
