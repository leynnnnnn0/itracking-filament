<?php

namespace App\Traits;

trait HasRedirectUrl
{
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index'); 
    }
}
