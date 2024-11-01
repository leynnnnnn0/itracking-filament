<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftDeleteRecord extends Model
{
    use HasFactory;

    protected $table = 'soft_delete_records';
    public $timestamps = false;

    public function save(array $options = [])
    {
        return false;
    }
}
