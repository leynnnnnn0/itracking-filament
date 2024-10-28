<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;

enum UserRole: string implements HasColor
{
    case ADMIN = 'Admin';
    case SUB_ADMIN = 'Sub Admin';

    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ADMIN => 'success',
            self::SUB_ADMIN => 'warning',
        };
    }
}
