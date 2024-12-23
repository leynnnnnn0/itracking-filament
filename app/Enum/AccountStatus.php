<?php

namespace App\Enum;

enum AccountStatus : string {
    case ACTIVE = 'active';
    case DEACTIVATED = 'deactivated';

    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }
}

