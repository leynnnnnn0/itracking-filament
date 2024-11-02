<?php

namespace App\Listeners;

use App\Models\AccountableOfficer;
use App\Models\BorrowedEquipment;
use App\Models\Personnel;
use App\Models\Supply;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetNotification
{
    public function handle(Login $event): void
    {
        $recipient = Auth::user();

        DB::table('notifications')->delete();

        $this->checkLowStockSupplies($recipient);
        $this->checkSupplyExpirations($recipient);
        $this->checkAccountableOfficerExpirations($recipient);
        $this->checkPersonnelExpirations($recipient);
        $this->checkEquipmentBorrowings($recipient);
    }

    private function checkLowStockSupplies($recipient): void
    {
        $lowStockSupplies = Supply::where('total', '<', 10)
            ->orderBy('total', 'asc')
            ->get();

        foreach ($lowStockSupplies as $supply) {
            Notification::make()
                ->title("Low Stock Alert: {$supply->description}")
                ->body("Only {$supply->total} {$supply->unit} remaining (ID# {$supply->id})\n\nPlease restock immediately.")
                ->danger()
                ->persistent()
                ->sendToDatabase($recipient);
        }
    }

    private function checkSupplyExpirations($recipient): void
    {
        $expiringSupplies = Supply::where('expiry_date', '<', now()->addWeek())
            ->get();

        foreach ($expiringSupplies as $supply) {
            $isExpired = $supply->expiry_date <= today();
            
            $status = $isExpired ? 'EXPIRED' : 'Expiring ' . $supply->expiry_date->diffForHumans();
            $title = $isExpired ? 'Expired Supply' : 'Supply Expiring Soon';
            
            Notification::make()
                ->title("{$title}: {$supply->description}")
                ->body("Status: {$status}\nID# {$supply->id}\n\nTake necessary action immediately.")
                ->when($isExpired, fn($notification) => $notification->danger(), fn($notification) => $notification->warning())
                ->persistent()
                ->sendToDatabase($recipient);
        }
    }

    private function checkAccountableOfficerExpirations($recipient): void
    {
        $endingContracts = AccountableOfficer::where('end_date', '<', now()->addWeek())
            ->where('end_date', '>', today())
            ->get();

        foreach ($endingContracts as $contract) {
            Notification::make()
                ->title("Contract Expiring: {$contract->name}")
                ->body("Accountable Officer contract expires {$contract->end_date->diffForHumans()}\n" .
                    "ID# {$contract->id}\n\nInitiate renewal process.")
                ->warning()
                ->persistent()
                ->sendToDatabase($recipient);
        }
    }

    private function checkPersonnelExpirations($recipient): void
    {
        $endingContracts = Personnel::where('end_date', '<', now()->addWeek())
            ->where('end_date', '>', today())
            ->get();

        foreach ($endingContracts as $contract) {
            Notification::make()
                ->title("Contract Expiring: {$contract->name}")
                ->body("Personnel contract expires {$contract->end_date->diffForHumans()}\n" .
                    "ID# {$contract->id}\n\nInitiate renewal process.")
                ->warning()
                ->persistent()
                ->sendToDatabase($recipient);
        }
    }

    private function checkEquipmentBorrowings($recipient): void
    {
        // Check equipment due in next 2 days
        $toReturnEquipment = BorrowedEquipment::where('end_date', '<', now()->addDays(2))
            ->whereNull('returned_date')
            ->get();

        foreach ($toReturnEquipment as $equipment) {
            Notification::make()
                ->title("Equipment Due Soon: {$equipment->equipment_name}")
                ->body("Borrowed by: {$equipment->borrower_full_name}\n" .
                    "Due: {$equipment->end_date->diffForHumans()}\n" .
                    "ID# {$equipment->id}\n\nFollow up on return.")
                ->warning()
                ->persistent()
                ->sendToDatabase($recipient);
        }

        // Check overdue equipment
        $overDueEquipment = BorrowedEquipment::where('end_date', '<', today())
            ->whereNull('returned_date')
            ->get();

        foreach ($overDueEquipment as $equipment) {
            Notification::make()
                ->title("Overdue Equipment: {$equipment->equipment_name}")
                ->body("Borrowed by: {$equipment->borrower_full_name}\n" .
                    "Was due: {$equipment->end_date->diffForHumans()}\n" .
                    "ID# {$equipment->id}\n\nRequires immediate attention.")
                ->danger()
                ->persistent()
                ->sendToDatabase($recipient);
        }
    }
}