<?php

namespace App\Exports;

use App\Models\SupplyHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplyHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $query;
    public function headings(): array
    {
        return [
            'Supply ID',
            'Supply Name',
            'Quantity',
            'Used',
            'Missing',
            'Expired',
            'Added',
            'Total',
        ];
    }

    public function map($supply): array
    {
        return [
            $supply->supply_id,
            $supply->supply->description,
            $supply->quantity ?: "0",
            $supply->used ?: "0",
            $supply->missing ?: "0",
            $supply->expired ?: "0",
            $supply->added ?: "0",
            $supply->total ?: "0",
        ];
    }

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }
}
