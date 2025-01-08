<?php

namespace App\Enums;

use App\traits\EnumHelpers;

enum WorkLocation: string
{
    use EnumHelpers;

    case Onsite = "onsite";
    case Remote = "remote";
    case Hyprid = "hyprid";
}
