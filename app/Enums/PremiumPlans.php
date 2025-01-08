<?php

namespace App\Enums;

use App\traits\EnumHelpers;

enum PremiumPlans: string
{
    use EnumHelpers;

    case Monthly = "monthly";
    case Yearly = "yearly";
}
