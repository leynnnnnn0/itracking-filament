<?php

namespace App\Enum;

enum UserRole : string
{
    case ADMIN = 'Admin';
    case SUB_ADMIN = 'Sub Admin';

    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }
}
