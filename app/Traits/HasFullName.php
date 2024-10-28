<?php

namespace App\Traits;

trait HasFullName
{
    public function getFullNameAttribute()
    {
        return "$this->first_name $this->last_name";
    }
}
