<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    /** @use HasFactory<\Database\Factories\OfficeFactory> */
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function accounting_officers()
    {
        return $this->hasMany(AccountingOfficer::class);
    }

    public function personnel()
    {
        return $this->hasMany(Personnel::class);
    }
}
