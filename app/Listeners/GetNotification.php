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

        if ($lowStockSupplies->count() > 0) {
            Notification::make()
                ->title("Inventory Management: Critical Stock Levels")
                ->body($this->formatLowStockMessage($lowStockSupplies))
                ->danger()
                ->persistent()
                ->sendToDatabase($recipient);
        }
    }

    private function checkSupplyExpirations($recipient): void
    {
        $expiringSupplies = Supply::where('expiry_date', '<', now()->addWeek())
            ->get();

        if ($expiringSupplies->count() > 0) {
            Notification::make()
                ->title("Inventory Alert: Upcoming Supply Expirations")
                ->body($this->formatExpiryMessage($expiringSupplies))
                ->warning()
                ->persistent()
                ->sendToDatabase($recipient);
        }
    }

    private function checkAccountableOfficerExpirations($recipient): void
    {
        $endingContracts = AccountableOfficer::where('end_date', '<', now()->addWeek())
            ->where('end_date', '>', today())
            ->get();

        if ($endingContracts->count() > 0) {
            Notification::make()
                ->title("Human Resources: Accountable Officer Contracts Expiring")
                ->body($this->formatContractExpirationMessage($endingContracts, 'Accountable Officer'))
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

        if ($endingContracts->count() > 0) {
            Notification::make()
                ->title("Human Resources: Personnel Contracts Expiring")
                ->body($this->formatContractExpirationMessage($endingContracts, 'Personnel'))
                ->warning()
                ->persistent()
                ->sendToDatabase($recipient);
        }
    }

    private function checkEquipmentBorrowings($recipient): void
    {
        $toReturnEquipment = BorrowedEquipment::where('end_date', '<', now()->addDays(2))
            ->whereNull('returned_date')
            ->get();

        $overDueEquipment = BorrowedEquipment::where('end_date', '<', today())
            ->whereNull('returned_date')
            ->get();

        $notifications = [];

        if ($toReturnEquipment->count() > 0) {
            $notifications[] = [
                'title' => "Equipment Management: Upcoming Returns",
                'body' => $this->formatEquipmentBorrowingMessage($toReturnEquipment, 'due'),
                'type' => 'warning'
            ];
        }

        if ($overDueEquipment->count() > 0) {
            $notifications[] = [
                'title' => "Equipment Management: Overdue Equipment",
                'body' => $this->formatEquipmentBorrowingMessage($overDueEquipment, 'overdue'),
                'type' => 'danger'
            ];
        }

        foreach ($notifications as $notification) {
            Notification::make()
                ->title($notification['title'])
                ->body($notification['body'])
                ->{$notification['type']}()
                ->persistent()
                ->sendToDatabase($recipient);
        }
    }

    private function formatLowStockMessage($supplies): string
    {
        $messages = $supplies->map(function ($supply) {
            return "• {$supply->description} (ID# {$supply->id}): Only {$supply->total} {$supply->unit} remaining";
        });

        return "Critical inventory levels detected:\n" .
            $messages->implode("\n") .
            "\n\nImmediate restocking is required to maintain operational readiness.";
    }

    private function formatExpiryMessage($supplies): string
    {
        $expiredItems = $supplies->where('expiry_date', '<=', today());
        $expiringItems = $supplies->where('expiry_date', '>', today());

        $messages = [];

        if ($expiredItems->count() > 0) {
            $expiredMessages = $expiredItems->map(function ($supply) {
                return "• {$supply->description} (ID# {$supply->id}): EXPIRED";
            });
            $messages[] = "Expired Inventory:\n" . $expiredMessages->implode("\n");
        }

        if ($expiringItems->count() > 0) {
            $expiringMessages = $expiringItems->map(function ($supply) {
                return "• {$supply->description} (ID# {$supply->id}): Expires {$supply->expiry_date->diffForHumans()}";
            });
            $messages[] = "Inventory Nearing Expiration:\n" . $expiringMessages->implode("\n");
        }

        return implode("\n\n", $messages) .
            "\n\nReview and take necessary actions to mitigate risks.";
    }

    private function formatContractExpirationMessage($contracts, $type): string
    {
        $messages = $contracts->map(function ($contract) {
            return "• {$contract->name} (ID# {$contract->id}): Expires {$contract->end_date->diffForHumans()}";
        });

        return "Upcoming {$type} Contract Expirations:\n" .
            $messages->implode("\n") .
            "\n\nInitiate contract renewal or replacement process.";
    }

    private function formatEquipmentBorrowingMessage($equipment, $status): string
    {
        $messages = $equipment->map(function ($item) use ($status) {
            return "• {$item->equipment_name} (ID# {$item->id}): Borrowed by {$item->borrower_full_name}, {$status} {$item->end_date->diffForHumans()}";
        });

        $statusText = $status === 'due' ? 'Upcoming' : 'Overdue';

        return "{$statusText} Equipment Returns:\n" .
            $messages->implode("\n") .
            "\n\nTake immediate action to manage equipment returns.";
    }
}
