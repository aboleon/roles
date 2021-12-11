<?php

namespace Aboleon\Roles\Traits;

use Aboleon\Roles\Models\Role;
use Aboleon\Roles\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait Roles
{
    public function user_roles()
    {
        return cache()->rememberForever('aboleon_roles', function() {
           return Role::pluck('name','id')->toArray();
        });
    }

    public function scopeWithRole($query, string|array|null $role = null)
    {
        if ($role) {
            $roles = [];
            if (is_string($role)) {
                $roles[] = array_search($role, $this->user_roles());
            } elseif(is_array($role)) {
                foreach($role as $value) {
                    $roles[] = array_search($value, $this->user_roles());
                }
            }

            $roles = array_filter($roles);
            $query->whereHas('roles',function (Builder $subQuery) use ($roles) {
                $subQuery->whereIn('role_id',$roles);
            });
        }
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class,'user_id');
    }

    public function hasRole(string|array $role): bool
    {
        if ($role) {
            if (is_string($role)) {
                $role = array_map(fn($x) =>trim(str_replace(["'",'"'],'',$x)), explode(',', $role));
            }

            $roles = [];
            foreach($role as $value) {
                $roles[] = array_search($value, $this->user_roles());
            }
            $roles = array_filter($roles);

            return $roles && (bool)collect($this->roles)->whereIn('role_id', $roles)->count();
        }
        return false;
    }

}