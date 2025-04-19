<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum WorkLocation: string
{
    use EnumHelpers;

    case Onsite = 'Onsite';
    case Remote = 'Remote';
    case Hyprid = 'Hybrid';
}
