<?php

namespace App\Observers;

use App\Models\Supply;
use App\Models\SupplyHistory;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SupplyObserver
{
    public function created(Supply $supply)
    {
        try {
            SupplyHistory::create([
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
    /**
     * Handle the Supply "updated" event.
     */
    public function updated(Supply $supply): void
    {
        // Get the changed attributes
        $changes = $supply->getChanges();

        // Check if quantity, used, or total were changed
        if (isset($changes['quantity']) || isset($changes['used']) || isset($changes['total'])) {
            SupplyHistory::create([
                'supply_id' => $supply->id,
                'quantity' => $supply->quantity,
                'missing' => $supply->missing,
                'expired' => $supply->expired,
                'used' => $supply->used,
                'added' => $supply->recently_added ?? 0,
                'total' => $supply->total
            ]);
        }
    }
}
