<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowedEquipment extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowedEquipmentFactory> */
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'quantity',
        'borrower_first_name',
        'borrower_last_name',
        'borrower_phone_number',
        'borrower_email',
        'start_date',
        'end_date',
        'returned_date'
    ];

    public function casts()
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'returned_date' => 'date',
        ];
    }

}
