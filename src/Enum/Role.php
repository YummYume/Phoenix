<?php

namespace App\Enum;

use App\Enum\Traits\UtilsTraits;

enum Role: string {
    use UtilsTraits;

    case User = 'ROLE_USER';
    case Admin = 'ROLE_ADMIN';
    case SuperAdmin = 'ROLE_SUPER_ADMIN';
}
