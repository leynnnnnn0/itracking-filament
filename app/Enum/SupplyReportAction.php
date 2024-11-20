<?php

namespace App\Enum;

enum SupplyReportAction: string
{
    case DISPENSE = 'dispense';
    case ADD = 'add';

    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }
}
