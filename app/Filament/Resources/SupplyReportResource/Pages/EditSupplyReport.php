<?php

namespace App\Filament\Resources\SupplyReportResource\Pages;

use App\Filament\Resources\SupplyReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplyReport extends EditRecord
{
    protected static string $resource = SupplyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
