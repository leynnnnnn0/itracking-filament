<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class OfficeAgency extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\OfficeAgencyFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name'
    ];

    public function borrowed_equipment()
    {
        return $this->hasMany(BorrowedEquipment::class);
    }
}
