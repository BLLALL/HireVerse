<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum ExperienceLevel: string
{
    use EnumHelpers;
    case Junior = 'Junior';
    case MidLevel = 'Mid-level';
    case Senior = 'Senior';
    case Intern = 'Intern';
}
