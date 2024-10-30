<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyCategory extends Model
{
    /** @use HasFactory<\Database\Factories\SupplyCategoryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supply_id',
        'category_id'
    ];
}
