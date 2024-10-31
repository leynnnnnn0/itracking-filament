<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Office extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\OfficeFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name'
    ];

    public function accountable_officers()
    {
        return $this->hasMany(AccountableOfficer::class);
    }

    public function personnel()
    {
        return $this->hasMany(Personnel::class);
    }
}
