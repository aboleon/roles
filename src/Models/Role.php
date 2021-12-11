<?php

namespace Aboleon\Roles\Models;

use Aboleon\Roles\Repositories\Tables;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = Tables::fetch('roles');
    }


}