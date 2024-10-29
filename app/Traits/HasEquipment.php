<?php

namespace App\Traits;

use App\Models\Equipment;

trait HasEquipment
{
    public function equipment(): mixed
    {
        return $this->hasMany(Equipment::class);
    }
}
