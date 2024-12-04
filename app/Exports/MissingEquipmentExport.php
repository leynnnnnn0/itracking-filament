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
            'Equipment PN',
            'Equipment ID',
            'Quantity',
            'Quantity Found',
            'Status',
            'Description',
            'Reported By',
            'Reported Date',
            'Is Condemned',
            'remarks',
        ];
    }
    public function map($borrowedEquipment): array
    {
        return [
            $borrowedEquipment->equipment->property_number,
            $borrowedEquipment->equipment->name,
            $borrowedEquipment->quantity,
            $borrowedEquipment->quantity_found,
            $borrowedEquipment->status,
            $borrowedEquipment->description,
            $borrowedEquipment->reported_by,
            $borrowedEquipment->reported_date ? $borrowedEquipment->reported_date: null,
            $borrowedEquipment->is_condemned ? 'Yes' : 'No',
            $borrowedEquipment->remarks,
        ];
    }
}
