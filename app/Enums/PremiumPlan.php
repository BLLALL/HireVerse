<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum PremiumPlan: string
{
    use EnumHelpers;

    case Monthly = 'monthly';
    case Yearly = 'yearly';
}
