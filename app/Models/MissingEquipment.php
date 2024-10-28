<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissingEquipment extends Model
{
    /** @use HasFactory<\Database\Factories\MissingEquipmentFactory> */
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'quantity',
        'status',
        'description',
        'reported_by',
        'reported_date',
        'is_condemned'
    ];

    public function casts()
    {
        return [
            'reported_date' => 'date',
            'is_condemned' => 'boolean'
        ];
    }
}
