<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;

enum SupplyReportAction: string implements HasColor
{
    case DISPENSE = 'dispense';
    case ADD = 'add';

    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ADD => 'success',
            self::DISPENSE => 'warning',
        };
    }
}
