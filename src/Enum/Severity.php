<?php

namespace App\Enum;

use App\Enum\Traits\UtilsTraits;

enum Severity: string {
    use UtilsTraits;

    case Breaking = 'breaking';
    case VeryHigh = 'very_high';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
    case VeryLow = 'very_low';
    case CanBeIgnored = 'can_be_ignored';
}
