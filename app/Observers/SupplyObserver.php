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
                'missing' => 0,
                'expired' => 0,
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
        $recipient = Auth::user();

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

            $percentageLeft = ($supply->total / $supply->quantity) * 100;

            if ($percentageLeft <= 15) {
                Notification::make()
                    ->title("Low Stock Alert: {$supply->description}")
                    ->body(
                        "Only {$supply->total} {$supply->unit} remaining (" . number_format($percentageLeft, 1) . "% of total stock)\n" .
                            "ID# {$supply->id}\n\n" .
                            "Please restock immediately."
                    )
                    ->danger()
                    ->persistent()
                    ->sendToDatabase($recipient);
            }
        }
    }
}
