<?php

namespace App\Filament\Resources\SupplyReportResource\Pages;

use App\Enum\SupplyReportAction;
use App\Filament\Resources\SupplyReportResource;
use App\Traits\HasConfirmationModal;
use App\Traits\HasRedirectUrl;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateSupplyReport extends CreateRecord
{
    use HasRedirectUrl, HasConfirmationModal;
    protected static string $resource = SupplyReportResource::class;
    protected static bool $canCreateAnother = false;

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                DB::transaction(function () {
                    $this->create();

                    $supplyReport = $this->record;
                    $supply = $supplyReport->supply;

                    if ($supplyReport->action === SupplyReportAction::ADD->value) {
                        $supply->quantity += $supplyReport->quantity;
                        $supply->recently_added = $supplyReport->quantity;
                        $supply->total += $supplyReport->quantity;
                    } else {
                        $supply->total -= $supplyReport->quantity;
                        $supply->used += $supplyReport->quantity;
                        $supply->recently_added = 0;
                    }

                    $supply->save();
                });
            });
    }
}
