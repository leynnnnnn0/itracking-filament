<?php

namespace App\Exports;

use App\Models\Personnel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PersonnelExport implements FromCollection, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }
    public function headings(): array
    {
        return [
            'ID',
            'Office',
            'Department',
            'Sub ICS/MR',
            'First Name',
            'Middle Name',
            'Last Name',
            'Office Phone',
            'Office Email',
            'Start Date',
            'End Date',
            'Remarks'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->office->name,
            $user->department->name,
            $user->sub_icsmfr?->name ?? 'N/a',
            $user->first_name,
            $user->middle_name,
            $user->last_name,
            $user->office_phone,
            $user->office_email,
            Carbon::parse($user->start_date)->format('F d, Y'),
            Carbon::parse($user->end_date)->format('F d, Y'),
            $user->remarks
        ];
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->query->get();
    }
}
