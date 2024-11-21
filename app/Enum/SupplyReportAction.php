<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;

enum SupplyReportAction: string implements HasColor
{
    case DISPENSE = 'dispense';
    case ADD = 'add';
    case RETURN = 'return';
    case FOUND = 'found';

    public static function values($exclude = [])
    {
        $data = array_column(self::cases(), 'value');
        $data = array_combine($data, $data);

        foreach ($exclude as $item) {
            unset($data[$item]);
        }

        return $data;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ADD => 'success',
            self::DISPENSE => 'warning',
            self::RETURN => 'gray',
            self::FOUND => 'primary'
        };
    }
}
