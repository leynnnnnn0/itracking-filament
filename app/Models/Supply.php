<?php

namespace App\Models;

use App\Observers\SupplyObserver;
use App\Traits\HasRedirectUrl;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([SupplyObserver::class])]
class Supply extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\SupplyFactory> */
    use HasFactory, SoftDeletes, HasRedirectUrl, \OwenIt\Auditing\Auditable, Notifiable;

    protected $fillable = [
        'description',
        'unit',
        'quantity',
        'missing',
        'expired',
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

    public function supply_history()
    {
        return $this->hasMany(SupplyHistory::class);
    }

    public function supply_incidents()
    {
        return $this->hasMany(SupplyIncident::class);
    }


    public function categories()
    {
        return $this->belongsToMany(Category::class, 'supply_categories');
    }

    public function getSelectDisplayAttribute()
    {
        return "$this->description (ID# $this->id)";
    }

    public function supply_report()
    {
        return $this->hasMany(SupplyReport::class);
    }
}
