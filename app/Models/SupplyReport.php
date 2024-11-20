<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class SupplyReport extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\SupplyReportFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'supply_id',
        'handler',
        'quantity',
        'remarks',
        'quantity_returned',
        'date_acquired',
        'action'
    ];

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
}
