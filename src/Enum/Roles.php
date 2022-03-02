<?php

namespace App\Enum;

enum Roles: string {
    case User = 'ROLE_USER';
    case Admin = 'ROLE_ADMIN';
    case SuperAdmin = 'ROLE_SUPER_ADMIN';
}
