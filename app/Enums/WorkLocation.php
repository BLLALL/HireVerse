<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum WorkLocation: string
{
    use EnumHelpers;

    case Onsite = "onsite";
    case Remote = "remote";
    case Hyprid = "hyprid";
}
