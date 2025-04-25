<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum JobPhase: string
{
    use EnumHelpers;

    case CVFiltration = 'CV filtration';
    case Revision = 'CV revision';
    case Interview = 'Interview';
}
