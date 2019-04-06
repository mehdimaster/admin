<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class USRUserPermissions extends Model
{
	protected $table = 'usr_user_permissions';

    public function user()
    {
        return $this->belongsTo(User::class);
	}

    public function permission()
    {
        return $this->belongsTo("\App\USRPermission");
	}

}