<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum ReviewRating: string
{
    use EnumHelpers;

    case Poor = "poor";
    case Fair = "fair";
    case Average = "average";
    case Good = "good";
    case Excellent = "excellent";
}
