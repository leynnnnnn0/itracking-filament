<?php

namespace App;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Colors\Color;

enum BorrowStatus: string implements HasColor
{
    case BORROWED = 'borrowed';
    case PARTIALLY_RETURNED = 'partially_returned';
    case RETURNED = 'returned';
    case PARTIALLY_MISSING = 'partially_missing';
    case MISSING = 'missing';
    case RETURNED_WITH_MISSING = 'returned_with_missing';
    case PARTIALLY_RETURNED_WITH_MISSING = 'partially_returned_with_missing';

    public static function values()
    {
        $data = array_column(self::cases(), 'value');
        return array_combine($data, $data);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BORROWED => 'primary',
            self::PARTIALLY_RETURNED => 'info',
            self::RETURNED => 'success',
            self::PARTIALLY_MISSING => 'warning',
            self::MISSING => 'danger',
            self::RETURNED_WITH_MISSING => 'warning',
            self::PARTIALLY_RETURNED_WITH_MISSING => 'warning',
        };
    }
}
