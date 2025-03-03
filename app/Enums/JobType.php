<?php

namespace App\Enums;
use App\Traits\EnumHelpers;

enum JobType: string
{
    use EnumHelpers;

    case PartTime = "part_time";
    case FullTime = "full_time";
    case Freelance = "freelance";
}
