<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum ApplicationStatus: string
{
    use EnumHelpers;

    case Eligible = 'cv_eligible';
    case Rejected = 'rejected';
    case Accepted = 'accepted';
    case Pending = 'pending';
    case Interviewed = 'interviewed';
}
