<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum ExperienceLevel: string
{
    use EnumHelpers;
    case Junior = "junior";
    case MidLevel = "mid-level";
    case Senior = "senior";
    case Intern = "intern";
}
