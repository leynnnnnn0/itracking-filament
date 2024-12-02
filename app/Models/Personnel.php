<?php

namespace App\Models;

use App\Traits\HasEquipment;
use App\Traits\HasFullName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Personnel extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\PersonnelFactory> */
    use HasFactory, HasFullName, HasEquipment, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'office_id',
        'department_id',
        'sub_icsmfr_id',
        'first_name',
        'middle_name',
        'last_name',
        'office_phone',
        'office_email',
        'start_date',
        'end_date',
        'remarks',
    ];

    protected $table = 'personnel';

    public function casts()
    {
        return [

            'start_date' => 'date',
            'end_date' => 'date'
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function equipment_history()
    {
        return $this->hasMany(EquipmentHistory::class);
    }

    public function sub_icsmfr()
    {
        return $this->belongsTo(SubICSMFR::class);
    }
}
