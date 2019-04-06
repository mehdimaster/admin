<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class USRRole extends Model
{
	protected $table = 'usr_roles';

    public function roleUser()
    {
        return $this->hasMany("\App\USRRoleUsers","role_id");
	}

    public function rolePermissions()
    {
        return $this->hasMany("\App\USRRolePermissions" , "role_id");
	}
}