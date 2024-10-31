<?php

namespace App\Models;

use App\Traits\HasEquipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class OrganizationUnit extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\OrganizationUnitFactory> */
    use HasFactory, HasEquipment, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name'
    ];

}
