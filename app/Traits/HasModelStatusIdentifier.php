<?php

namespace App\Traits;

use App\BorrowStatus;
use App\Enum\EquipmentStatus;

trait HasModelStatusIdentifier
{
    public static function getBorrowStatus($borrowedEquipment)
    {
        $totalMissingQuantity = $borrowedEquipment->total_quantity_missing;
        $borrowedQuantity = $borrowedEquipment->quantity;
        $totalReturnedQuantity = $borrowedEquipment->total_quantity_returned;

        // Fully missing
        if ($totalMissingQuantity === $borrowedQuantity) {
            return BorrowStatus::MISSING->value;
        }

        // Partially returned with missing items
        if ($totalMissingQuantity > 0 && $totalReturnedQuantity > 0 && $borrowedQuantity !== ($totalMissingQuantity + $totalReturnedQuantity)) {
            return BorrowStatus::PARTIALLY_RETURNED_WITH_MISSING->value;
        }

        // Returned with missing items (no partial returns)
        if ($totalMissingQuantity > 0 && $borrowedQuantity === ($totalMissingQuantity + $totalReturnedQuantity)) {
            return BorrowStatus::RETURNED_WITH_MISSING->value;
        }

        // Partially missing (no returns)
        if ($totalMissingQuantity > 0 && $totalReturnedQuantity === 0) {
            return BorrowStatus::PARTIALLY_MISSING->value;
        }

        // Fully returned with no missing items
        if ($totalMissingQuantity === 0 && $totalReturnedQuantity === $borrowedQuantity) {
            return BorrowStatus::RETURNED->value;
        }

        // Partially returned with no missing items
        if ($totalMissingQuantity === 0 && $totalReturnedQuantity < $borrowedQuantity) {
            return BorrowStatus::PARTIALLY_RETURNED->value;
        }

        // Default case: still borrowed
        return BorrowStatus::BORROWED->value;
    }

    public static function getEquimentStatus($equipment)
    {
        $totalAvailableQuantity = $equipment->quantity_available;
        $totalBorrowedQuantity = $equipment->quantity_borrowed;
        // Full borrowed is when total avaliable is 0 and qunatity borrowed is greater than 0]
        if ($totalAvailableQuantity === 0 && $totalBorrowedQuantity > 0)
            return EquipmentStatus::FULLY_BORROWED->value;
        // Partaially borrowed is when totalAvailable qunatity is not equals to 0 and borrowed is greater than 0
        if ($totalAvailableQuantity > 0 && $totalBorrowedQuantity > 0)
            return EquipmentStatus::PARTIALLY_BORROWED->value;
        if ($totalAvailableQuantity === 0 && $totalBorrowedQuantity === 0 && $equipment->quantity_condemned === $equipment->quantity)
            return EquipmentStatus::CONDEMNED->value;

        return EquipmentStatus::ACTIVE->value;
    }
}
