<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class SupplyIncident extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\SupplyIncidentFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'supply_id',
        'type',
        'quantity',
        'remarks',
        'incident_date',
        'status'
    ];

    protected $casts = [
        'incident_date' => 'date'
    ];

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function getSelectDisplayAttribute()
    {
        return "$this->description (ID# $this->id)";
    }
}
