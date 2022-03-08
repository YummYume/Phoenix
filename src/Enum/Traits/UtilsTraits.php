<?php

namespace App\Enum\Traits;

Trait UtilsTraits
{
    public static function random(): self {
        return self::cases()[array_rand(self::cases())];
    }
}
