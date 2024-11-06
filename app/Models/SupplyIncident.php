<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyIncident extends Model
{
    /** @use HasFactory<\Database\Factories\SupplyIncidentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supply_id',
        'type',
        'quantity',
        'remarks',
        'incident_date'
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
