<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum QuestionDifficulty: string
{
    use EnumHelpers;

    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';
}
