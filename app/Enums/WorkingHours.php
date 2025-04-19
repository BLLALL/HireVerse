<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum WorkingHours: string
{
    use EnumHelpers;
    case FixedShecdule = 'Fixed schedule';
    case FlexibleShcedule = 'Flexible schedule';
}
