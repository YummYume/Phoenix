<?php

namespace App\Enum;

use App\Enum\Traits\UtilsTraits;

enum Probability: string {
    use UtilsTraits;

    case Present = 'present';
    case VeryHigh = 'very_high';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
    case VeryLow = 'very_low';
    case Never = 'never';
}
