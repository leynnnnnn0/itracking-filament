<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    /** @use HasFactory<\Database\Factories\EquipmentFactory> */
    use HasFactory;

    protected $fillable = [
        'personnel_id',
        'accounting_officer_id',
        'organization_unit_id',
        'operating_unit_project_id',
        'fund_id',
        'personal_protective_equipment_id',
        'property_number',
        'quantity',
        'quantity_borrowed',
        'unit',
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

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function accounting_officer()
    {
        return $this->belongsTo(AccountingOfficer::class);
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


    public function personal_protective_equipment()
    {
        return $this->belongsTo(PersonalProtectiveEquipment::class);
    }
}
