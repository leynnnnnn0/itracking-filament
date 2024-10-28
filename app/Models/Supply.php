<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    /** @use HasFactory<\Database\Factories\SupplyFactory> */
    use HasFactory;

    protected $fillable = [
        'description',
        'unit',
        'quantity',
        'used',
        'recently_added',
        'total',
        'expiry_date',
        'is_consumable'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_consumable' => 'boolean',
        'quantity' => 'integer',
        'used' => 'integer',
        'total' => 'integer'
    ];
}
