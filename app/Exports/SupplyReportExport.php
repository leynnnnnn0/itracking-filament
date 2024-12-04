<?php

namespace App\Exports;

use App\Models\SupplyReport;
use App\Traits\HasExcelDownload;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplyReportExport implements FromCollection, WithHeadings, WithMapping
{
    use HasExcelDownload;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            'Supply ID',
            'Supply Name',
            'Handler',
            'Quantity',
            'Remarks',
            'Quantity Returned',
            'Date Acquired',
            'Action',
        ];
    }

    public function map($supply): array
    {
        return [
            $supply->supply_id,
            $supply->supply->description,
            $supply->handler,
            $supply->quantity ?: '0',
            $supply->remarks,
            $supply->quantity_returned ?: '0',
            $supply->date_acquired ? $supply->date_acquired : null,
            $supply->action,
        ];
    }
}
