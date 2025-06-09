<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum QuestionDifficulty: string
{
    use EnumHelpers;

    case Easy = 'Easy';
    case Medium = 'Medium';
    case Hard = 'Hard';
}
