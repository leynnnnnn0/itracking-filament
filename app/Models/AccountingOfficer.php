<?php

namespace App\Models;

use App\Traits\HasEquipment;
use App\Traits\HasFullName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingOfficer extends Model
{
    /** @use HasFactory<\Database\Factories\AccountingOfficerFactory> */
    use HasFactory, HasFullName, HasEquipment;

    protected $fillable = [
        'office_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone_number'
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
