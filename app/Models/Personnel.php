<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    /** @use HasFactory<\Database\Factories\PersonnelFactory> */
    use HasFactory;

    protected $fillable = [
        'office_id',
        'department_id',
        'position_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone_number',
        'email',
        'start_date',
        'end_date',
        'remarks'
    ];

    protected $table = 'personnel';

    public function casts()
    {
        return [
            'date_of_birth' => 'date',
            'start_date' => 'date',
            'end_date' => 'date'
        ];
    }
}
