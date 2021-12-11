<?php

namespace Aboleon\Roles\Models;

use Aboleon\Roles\Repositories\Tables;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = Tables::fetch('users_roles');
    }


}