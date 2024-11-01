<?php

namespace App\Traits;

trait HasAuthorizationCheck
{
    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'Admin';
    }
}
