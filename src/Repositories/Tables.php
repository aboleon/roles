<?php

namespace Aboleon\Roles\Repositories;

class Tables {

    private static array $tables = [
        'roles' => 'aboleon_roles',
        'permissions' => 'aboleon_roles_permissions',
        'users_permissions' => 'aboleon_users_permissions',
        'roles_permissions' => 'aboleon_roles_permissions',
        'users_roles' => 'aboleon_users_roles'

    ];

    public static function fetch(string $table)
    {
        return self::$tables[$table];
    }

}