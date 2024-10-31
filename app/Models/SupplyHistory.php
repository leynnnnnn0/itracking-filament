<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class SupplyHistory extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\SupplyHistoryFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'supply_id',
        'quantity',
        'used',
        'added',
        'total',
        'created_at'
    ];

    public function casts()
    {
        return [
            'created_at' => 'datetime'
        ];
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
}
