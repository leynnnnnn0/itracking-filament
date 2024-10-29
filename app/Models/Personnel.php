<?php

namespace App\Models;

use App\Traits\HasEquipment;
use App\Traits\HasFullName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personnel extends Model
{
    /** @use HasFactory<\Database\Factories\PersonnelFactory> */
    use HasFactory, HasFullName, HasEquipment, SoftDeletes;

    protected $fillable = [
        'office_id',
        'department_id',
        'position_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'phone_number',
        'email',
        'start_date',
        'end_date',
        'remarks'
    ];

    protected $table = 'personnel';

    public function casts()
    {
        return [
            'date_of_birth' => 'date',
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

}
