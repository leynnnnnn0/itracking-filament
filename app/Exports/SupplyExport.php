<?php

namespace App\Exports;

use App\Models\Supply;
use App\Traits\HasExcelDownload;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplyExport implements FromCollection, WithHeadings, WithMapping
{
    use HasExcelDownload;

    public function headings(): array
    {
        return [
            'Id',
            'Description',
            'Unit',
            'Quantity',
            'Missing',
            'Expired',
            'Used',
            'Recently Added',
            'Total',
            'Expiry Date',
            'Is Consumable',
        ];
    }

    public function map($supply): array
    {
        return [
            $supply->id,
            $supply->description,
            $supply->unit,
            $supply->quantity ?: "0",
            $supply->missing ?: "0",
            $supply->expired ?: "0",
            $supply->used ?: "0",
            $supply->recently_added ?: "0",
            $supply->total ?: "0",
            $supply->expiry_date ? $supply->expiry_date->format('F d, Y') : null,
            $supply->is_consumable ? 'Yes' : 'No',
        ];
    }
}
