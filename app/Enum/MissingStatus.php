<?php

namespace App\Enum;

enum MissingStatus: string
{
    case REPORTED = 'Reported';
    case REPORTED_TO_SPMO = 'Reported to SPMO';
    case FOUND = 'Found';

    public static function values($exclude = [])
    {
        $data = array_column(self::cases(), 'value');
        $data = array_combine($data, $data);

        foreach ($exclude as $item) {
            unset($data[$item]);
        }

        return $data;
    }
}
