<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum JobType: string
{
    use EnumHelpers;

    case PartTime = 'Part-time';
    case FullTime = 'Full-time';
    case Freelance = 'Freelance';
}
