<?php

namespace Modules\UserManagement\Enum;

/**
 * @enum
 */
enum UserType: int {
    const ADMIN = 1;
    const MANAGER = 2;
    const STAFF = 3;
}
