<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class USRUsers extends Model
{
	protected $table = 'usr_users';

    public function person()
    {
        return $this->belongsTo('\App\USRPersons');
	}

    public function roleUser()
    {
        return $this->hasMany("\App\USRRoleUsers","user_id");
	}

    public function userPermission()
    {
        return $this->hasMany("\App\USRUserPermissions","user_id");
	}

}