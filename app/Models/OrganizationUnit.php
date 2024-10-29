<?php

namespace App\Models;

use App\Traits\HasEquipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationUnit extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationUnitFactory> */
    use HasFactory, HasEquipment, SoftDeletes;

    protected $fillable = [
        'name'
    ];

}
