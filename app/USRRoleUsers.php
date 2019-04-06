<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class USRRoleUsers extends Model
{
	protected $table = 'usr_role_users';

    public function user()
    {
        return $this->belongsTo(User::class);
	}

    public function role()
    {
        return $this->belongsTo("\App\USRRole");
	}


}