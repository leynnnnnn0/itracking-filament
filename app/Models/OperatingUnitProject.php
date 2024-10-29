<?php

namespace App\Models;

use App\Traits\HasEquipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatingUnitProject extends Model
{
    /** @use HasFactory<\Database\Factories\OperatingUnitProjectFactory> */
    use HasFactory, HasEquipment;

    protected $fillable = [
        'name'
    ];
}
