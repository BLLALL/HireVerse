<?php

namespace App\Enums;

use App\traits\EnumHelpers;

enum PremiumPlan: string
{
    use EnumHelpers;

    case Monthly = "monthly";
    case Yearly = "yearly";
}
