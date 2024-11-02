<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class MissingEquipment extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\MissingEquipmentFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'borrowed_equipment_id',
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

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
