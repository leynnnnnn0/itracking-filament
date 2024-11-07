<?php

namespace App\Enum;

enum SupplyIncidentStatus: string
{
    case ACTIVE = 'active';
    case FOUND = 'found';

    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }
}
