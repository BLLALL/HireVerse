<?php

namespace App;

enum JobType: string
{
    case PartTime = 'part-time';
    case FullTime = 'full-time';
    case Freelance = 'freelance';
}
