<?php

namespace App\Enum;

enum Gender: string
{
    case MALE = 'Male';
    case FEMALE = 'Female';

    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }
}
