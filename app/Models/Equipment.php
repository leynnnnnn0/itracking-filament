<?php

namespace App\Models;

use App\Observers\EquipmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


#[ObservedBy([EquipmentObserver::class])]
class Equipment extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\EquipmentFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'personnel_id',
        'accountable_officer_id',
        'organization_unit_id',
        'operating_unit_project_id',
        'fund_id',
        'sub_icsmfr_id',
        'unit',
        'property_number',
        'quantity',
        'quantity_available',
        'quantity_borrowed',
        'quantity_missing',
        'quantity_condemned',
        'name',
        'description',
        'date_acquired',
        'estimated_useful_time',
        'unit_price',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'is_borrowed' => 'boolean',
        'date_acquired' => 'date',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function getSelectDisplayAttribute()
    {
        return "$this->name (PN: $this->property_number)";
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function accountable_officer()
    {
        return $this->belongsTo(Personnel::class, 'accountable_officer_id');
    }

    public function operating_unit_project()
    {
        return $this->belongsTo(OperatingUnitProject::class);
    }

    public function organization_unit()
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function borrowed_equipment()
    {
        return $this->hasMany(BorrowedEquipment::class);
    }

    public function missing_equipment()
    {
        return $this->hasMany(MissingEquipment::class);
    }

    public function equipment_history()
    {
        return $this->hasMany(EquipmentHistory::class);
    }

    public function sub_icsmfr()
    {
        return $this->belongsTo(Personnel::class, 'sub_icsmfr_id');
    }
}
