<?php

namespace App\Exports;

use App\Models\MissingEquipment;
use App\Traits\HasExcelDownload;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MissingEquipmentExport implements FromCollection, WithHeadings, WithMapping
{
    use HasExcelDownload;
    public function headings(): array
    {
        return [
            'Borrowed Equipment',
            'Borrowed Equipment PN',
            'Equipment ID',
            'Quantity',
            'Quantity Found',
            'Status',
            'Description',
            'Reported By',
            'Reported Date',
            'Is Condemned',
        ];
    }
    public function map($borrowedEquipment): array
    {
        return [
            $borrowedEquipment->equipment->name,
            $borrowedEquipment->equipment->property_number,
            $borrowedEquipment->equipment_id,
            $borrowedEquipment->quantity,
            $borrowedEquipment->quantity_found,
            $borrowedEquipment->status,
            $borrowedEquipment->description,
            $borrowedEquipment->reported_by,
            $borrowedEquipment->reported_date ? $borrowedEquipment->reported_date->format('F d, Y') : null, // Format date if available
            $borrowedEquipment->is_condemned ? 'Yes' : 'No',
        ];
    }
}
