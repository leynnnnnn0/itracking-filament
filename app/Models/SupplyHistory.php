<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class SupplyHistory extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\SupplyHistoryFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'supply_id',
        'quantity',
        'used',
        'missing',
        'expired',
        'added',
        'total',
        'created_at'
    ];

    public function casts()
    {
        return [
            'created_at' => 'datetime'
        ];
    }
    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function scopeMonthlySummary(Builder $query, $start_date, $end_date)
    {
        return $query->whereBetween('created_at', [$start_date, $end_date])
            ->select([
                'supply_id',
                DB::raw("COALESCE((
                SELECT total 
                FROM supply_histories as prev_history 
                WHERE prev_history.supply_id = supply_histories.supply_id 
                AND prev_history.created_at < '{$start_date}'
                AND prev_history.deleted_at IS NULL
                ORDER BY prev_history.created_at DESC 
                LIMIT 1
            ), MAX(quantity) - SUM(added)) + SUM(added) as quantity"),
                DB::raw("MAX(used) - COALESCE((
                SELECT used 
                FROM supply_histories as prev_history 
                WHERE prev_history.supply_id = supply_histories.supply_id 
                AND prev_history.created_at < '{$start_date}'
                AND prev_history.deleted_at IS NULL
                ORDER BY prev_history.created_at DESC 
                LIMIT 1
            ), 0) as used"),
                DB::raw('SUM(added) as added'),
                DB::raw('MAX(expired) as expired'),
                DB::raw('MAX(missing) as missing'),
                DB::raw("(COALESCE((
                SELECT total 
                FROM supply_histories as prev_history 
                WHERE prev_history.supply_id = supply_histories.supply_id 
                AND prev_history.created_at < '{$start_date}'
                AND prev_history.deleted_at IS NULL
                ORDER BY prev_history.created_at DESC 
                LIMIT 1
            ), MAX(quantity) - SUM(added)) + SUM(added)) - (MAX(used) - COALESCE((
                SELECT used 
                FROM supply_histories as prev_history 
                WHERE prev_history.supply_id = supply_histories.supply_id 
                AND prev_history.created_at < '{$start_date}'
                AND prev_history.deleted_at IS NULL
                ORDER BY prev_history.created_at DESC 
                LIMIT 1
            ), 0)) as total"),
                DB::raw('MAX(created_at) as created_at')
            ])
            ->groupBy('supply_id');
    }
}
