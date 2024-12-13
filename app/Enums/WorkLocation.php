<?php

namespace App\Enums;

enum WorkLocation: string
{
    case Onsite = 'onsite';
    case Remote = 'remote';
    case Hyprid = 'hyprid';
}
