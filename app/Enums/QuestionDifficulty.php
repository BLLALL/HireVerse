<?php

namespace App\Enums;

use App\traits\EnumHelpers;

enum QuestionDifficulty: string
{
    use EnumHelpers;

    case Easy = "easy";
    case Medium = "medium";
    case Hard = "hard";
}
