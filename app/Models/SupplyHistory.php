<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyHistory extends Model
{
    /** @use HasFactory<\Database\Factories\SupplyHistoryFactory> */
    use HasFactory, SoftDeletes;

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