<?php

namespace App;

enum BorrowStatus: string
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
}
