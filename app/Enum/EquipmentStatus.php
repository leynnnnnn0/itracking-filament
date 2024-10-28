<?php

namespace App\Enum;

enum EquipmentStatus: string
{
    case ACTIVE = 'active';
    case PARTIALLY_BORROWED = 'partially_borrowed';
    case FULLY_BORROWED = 'fully_borrowed';
    case CONDEMNED = 'condemned';

    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }
}
