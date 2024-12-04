<?php

namespace App\Exports;

use App\Models\Equipment;
use App\Traits\HasExcelDownload;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EquipmentExport implements FromCollection, WithHeadings, WithMapping
{
    use HasExcelDownload;

    public function headings(): array
    {
        return [
            'Responsible Person',
            'Accountable Officer',
            'Organization Unit',
            'Operating Unit/Project',
            'Fund',
            'Unit',
            'Property Number',
            'Quantity',
            'Quantity Available',
            'Quantity Borrowed',
            'Quantity Missing',
            'Quantity Condemned',
            'Name',
            'Description',
            'Date Acquired',
            'Estimated Useful Time',
            'Unit Price',
            'Total Amount',
            'Status',
        ];
    }

    public function map($supply): array
    {
        return [
            $supply->personnel->full_name,
            $supply->accountable_officer->full_name,
            $supply->organization_unit->name,
            $supply->operating_unit_project->name,
            $supply->fund->name,
            $supply->unit,
            $supply->property_number,
            $supply->quantity ?: "0",
            $supply->quantity_available ?: "0",
            $supply->quantity_borrowed ?: "0",
            $supply->quantity_missing ?: "0",
            $supply->quantity_condemned ?: "0",
            $supply->name,
            $supply->description,
            $supply->date_acquired ? $supply->date_acquired->format('F d, Y') : null,
            $supply->estimated_useful_time,
            $supply->unit_price ?: "0",
            $supply->total_amount ?: "0",
            $supply->status,
        ];
    }
}
