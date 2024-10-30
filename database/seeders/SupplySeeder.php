<?php

namespace Database\Seeders;

use App\Models\Supply;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplySeeder extends Seeder
{
    protected $startDate = '2024-01-01';
    protected $historyPerSupply = 10; // Number of history entries per supply

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supply::factory(100)
            ->afterCreating(function ($supply) {
                $this->createSupplyHistory($supply);
            })
            ->create();
    }

    /**
     * Create history entries for a supply
     */
    protected function createSupplyHistory(Supply $supply): void
    {
        // Create initial history entry
        $this->createHistoryEntry($supply, [
            'quantity' => $supply->quantity,
            'used' => $supply->used,
            'added' => $supply->recently_added,
            'total' => $supply->total
        ]);

        // Create subsequent history entries
        for ($i = 0; $i < $this->historyPerSupply; $i++) {
            $this->createSupplyTransaction($supply);
        }
    }

    /**
     * Create a single transaction and history entry
     */
    protected function createSupplyTransaction(Supply $supply): void
    {
        $added = random_int(1, 30);
        $used = fake()->numberBetween(0, $supply->total);

        // Calculate new values
        $newQuantity = $added + $supply->quantity;
        $newUsed = $supply->used + $used;
        $newTotal = $this->calculateNewTotal($added, $newQuantity, $newUsed);

        // Update supply
        $supply->update([
            'recently_added' => $added,
            'used' => $newUsed,
            'quantity' => $newQuantity,
            'total' => $newTotal,
        ]);

        // Create history entry
        $this->createHistoryEntry($supply, [
            'quantity' => $newQuantity,
            'used' => $newUsed,
            'added' => $added,
            'total' => $newTotal,
        ]);
    }

    /**
     * Create a single history entry
     */
    protected function createHistoryEntry(Supply $supply, array $data): void
    {
        $data['created_at'] = $this->generateRandomDate();
        $supply->supplyHistory()->create($data);
    }

    /**
     * Calculate new total based on supply changes
     */
    protected function calculateNewTotal(int $added, int $quantity, int $used): int
    {
        return $quantity - $used;
    }

    /**
     * Generate a random date between start date and now
     */
    protected function generateRandomDate()
    {
        return fake()->dateTimeBetween($this->startDate, 'now');
    }
}
