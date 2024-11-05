<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class BorrowedEquipment extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\BorrowedEquipmentFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'equipment_id',
        'quantity',
        'borrower_first_name',
        'borrower_last_name',
        'borrower_phone_number',
        'borrower_email',
        'start_date',
        'end_date',
        'returned_date',
        'total_quantity_returned',
        'total_quantity_missing',
        'status',
        'remarks',
    ];

    public function casts()
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'returned_date' => 'date',
        ];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }


    public function getBorrowerFullNameAttribute()
    {
        return "$this->borrower_first_name $this->borrower_last_name";
    }

    public function getIsReturnedAttribute()
    {
        return $this->returned_date ? 'Yes' : 'No';
    }

    public function missing_equipment()
    {
        return $this->hasMany(MissingEquipment::class);
    }
}
