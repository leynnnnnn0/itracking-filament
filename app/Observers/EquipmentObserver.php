<?php

namespace App\Observers;

use App\Models\Equipment;
use App\Models\EquipmentHistory;

class EquipmentObserver
{
    /**
     * Handle the Equipment "created" event.
     */
    public function created(Equipment $equipment): void
    {
        EquipmentHistory::create([
            'equipment_id' => $equipment->id,
            'personnel_id' => $equipment->personnel_id,
            'accountable_officer_id' => $equipment->accountable_officer_id
        ]);
    }

    /**
     * Handle the Equipment "updated" event.
     */
    public function updated(Equipment $equipment): void
    {
        $changes = $equipment->getChanges();

        $responsible_person = isset($changes['personnel_id']) ? $equipment->personnel_id : null;
        $accountable_officer = isset($changes['accountable_officer_id']) ? $equipment->accountable_officer_id : null;

        if ($responsible_person || $accountable_officer)
            EquipmentHistory::create([
                'equipment_id' => $equipment->id,
                'personnel_id' => $responsible_person,
                'accountable_officer_id' => $accountable_officer
            ]);
    }

    /**
     * Handle the Equipment "deleted" event.
     */
    public function deleted(Equipment $equipment): void
    {
        //
    }

    /**
     * Handle the Equipment "restored" event.
     */
    public function restored(Equipment $equipment): void
    {
        //
    }

    /**
     * Handle the Equipment "force deleted" event.
     */
    public function forceDeleted(Equipment $equipment): void
    {
        //
    }
}
