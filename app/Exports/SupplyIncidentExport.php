<?php

namespace App\Exports;

use App\Models\SupplyIncident;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplyIncidentExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $query;
    public function headings(): array
    {
        return [
            'Supply ID',
            'Type',
            'Quantity',
            'Reconciled Quantity',
            'Remarks',
            'Incident Date',
            'Status',
        ];
    }

    public function map($supply): array
    {
        return [
            $supply->supply_id,
            $supply->type,
            $supply->quantity ?: "0",
            $supply->reconciled_quantity ?: "0",
            $supply->remarks,
            $supply->incident_date ? $supply->incident_date->format('Y-m-d') : "",
            $supply->status,
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
