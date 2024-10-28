<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatingUnitProject extends Model
{
    /** @use HasFactory<\Database\Factories\OperatingUnitProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'name'
    ];
}
