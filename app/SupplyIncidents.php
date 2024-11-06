<?php

namespace App;

enum SupplyIncidents: string
{
    case MISSING = 'missing';
    case EXPIRED = 'expired';
    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }
}
