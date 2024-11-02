<?php

namespace App\Enum;

enum MissingStatus: string
{
    case REPORTED = 'Reported';
    case REPORTED_TO_SPMO = 'Reported to SPMO';
    case FOUND = 'Found';

    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }
}
