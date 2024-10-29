<?php

namespace App\Models;

use App\Traits\HasEquipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalProtectiveEquipment extends Model
{
    /** @use HasFactory<\Database\Factories\PersonalProtectiveEquipmentFactory> */
    use HasFactory, HasEquipment;

    protected $fillable = [
        'name'
    ];
}
