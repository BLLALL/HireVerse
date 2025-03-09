<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum WorkingHours: string
{
    use EnumHelpers;
    case FixedShecdule = 'fixed_schedule';
    case FlexibleShcedule = 'flexible_schedule';
}
