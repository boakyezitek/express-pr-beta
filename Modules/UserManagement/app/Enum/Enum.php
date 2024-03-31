<?php
namespace Modules\UserManagement\Enum;

/**
 * @enum
 */
enum UserType: int {
    case ADMIN = 1;
    case MANAGER = 2;
    case STAFF = 3;
}
