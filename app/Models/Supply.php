<?php

namespace App\Models;

use App\Traits\HasRedirectUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Supply extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\SupplyFactory> */
    use HasFactory, SoftDeletes, HasRedirectUrl, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'description',
        'unit',
        'quantity',
        'used',
        'recently_added',
        'total',
        'expiry_date',
        'is_consumable'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_consumable' => 'boolean',
        'quantity' => 'integer',
        'used' => 'integer',
        'total' => 'integer'
    ];

    public function supplyHistory()
    {
        return $this->hasMany(SupplyHistory::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'supply_categories');
    }

    public function getSelectDisplayAttribute()
    {
        return "$this->description (ID# $this->id)";
    }
}
