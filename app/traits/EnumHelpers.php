<?php
namespace App\traits;

trait EnumHelpers
{
    public static function values(): array
    {
        return array_column(self::cases(), "value");
    }
}
