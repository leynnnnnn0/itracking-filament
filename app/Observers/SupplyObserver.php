<?php

namespace App\Observers;

use App\Models\Supply;
use App\Models\SupplyHistory;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class SupplyObserver
{
    public function created(Supply $supply)
    {
        $recipient = Auth::user();
        Notification::make()
            ->title('New supply created')
            ->body('A new supply has been created in the system')
            ->info()
            ->sendToDatabase($recipient);
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
                'used' => $supply->used,
                'added' => $supply->recently_added ?? 0,
                'total' => $supply->total
            ]);
        }
    }
}
