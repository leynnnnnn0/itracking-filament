<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Filament\Resources\SupplyResource;
use App\Models\MissingEquipment;
use App\Models\SupplyHistory;
use App\Traits\HasRedirectUrl;
use Exception;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateSupply extends CreateRecord
{
    use HasRedirectUrl;
    protected static string $resource = SupplyResource::class;

    protected function afterCreate()
    {

        try {
            $supply = $this->record;
            $history = SupplyHistory::create([
                'supply_id' => $supply->id,
                'quantity' => $supply->quantity,
                'used' => 0,
                'added' => 0,
                'total' => $supply->total,
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
