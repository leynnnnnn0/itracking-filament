<?php

namespace App\Exports;

use App\Models\BorrowedEquipment;
use App\Traits\HasExcelDownload;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BorrowedEquipmentExport implements FromCollection, WithMapping, WithHeadings
{
    use HasExcelDownload;
    public function headings(): array
    {
        return [
            'Office/Agency',
            'Equipment',
            'Quantity',
            'Borrower First Name',
            'Borrower Last Name',
            'Borrower Phone Number',
            'Borrower Email',
            'Start Date',
            'End Date',
            'Returned Date',
            'Total Quantity Returned',
            'Total Quantity Missing',
            'Status',
            'Remarks',
        ];
    }

    public function map($borrowedEquipment): array
    {
        return [
            $borrowedEquipment->office_agency->name,
            $borrowedEquipment->equipment->name,
            $borrowedEquipment->quantity ?: '0',
            $borrowedEquipment->borrower_first_name,
            $borrowedEquipment->borrower_last_name,
            $borrowedEquipment->borrower_phone_number,
            $borrowedEquipment->borrower_email,
            $borrowedEquipment->start_date ? $borrowedEquipment->start_date->format('F d, Y') : null,
            $borrowedEquipment->end_date ? $borrowedEquipment->end_date->format('F d, Y') : null,
            $borrowedEquipment->returned_date ? $borrowedEquipment->returned_date->format('F d, Y') : null,
            $borrowedEquipment->total_quantity_returned ?: '0',
            $borrowedEquipment->total_quantity_missing ?: '0',
            $borrowedEquipment->status,
            $borrowedEquipment->remarks,
        ];
    }
}
