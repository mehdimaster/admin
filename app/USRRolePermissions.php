<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class USRRolePermissions extends Model
{
	protected $table = 'usr_role_permissions';

    public function role()
    {
        return $this->belongsTo("\App\USRRole");
	}

    public function permission()
    {
        return $this->belongsTo("\App\USRPermission");
	}


}