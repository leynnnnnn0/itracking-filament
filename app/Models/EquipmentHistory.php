<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentHistory extends Model
{
    /** @use HasFactory<\Database\Factories\EquipmentHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'accountable_officer_id',
        'personnel_id',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function accountable_officer()
    {
        return $this->belongsTo(AccountableOfficer::class);
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }
}
