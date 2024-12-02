<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
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
            'First Name',
            'Middle Name',
            'Last Name',
            'Phone Number',
            'Email',
            'Role',
            'Status'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,        
            $user->first_name,    
            $user->middle_name,
            $user->last_name,        
            $user->phone_number,   
            $user->email,        
            $user->role,           
            $user->status          
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
