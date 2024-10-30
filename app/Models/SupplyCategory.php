<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyCategory extends Model
{
    /** @use HasFactory<\Database\Factories\SupplyCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'category_id'
    ];
}
