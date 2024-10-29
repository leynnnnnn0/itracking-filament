<?php

namespace App\Enum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;

enum EquipmentStatus: string implements HasColor
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

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::PARTIALLY_BORROWED => 'warning',
            self::FULLY_BORROWED => 'danger',
            self::CONDEMNED => 'gray'
        };
    }
}
