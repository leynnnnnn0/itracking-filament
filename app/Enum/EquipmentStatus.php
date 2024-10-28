<?php

namespace App\Enum;

enum EquipmentStatus : string
{
    case ACTIVE = 'active';
    case PARTIALLY_BORROWED = 'partially_borrowed';
    case FULLY_BORROWED = 'fully_borrowed';
    case CONDEMNED = 'condemned';
}
