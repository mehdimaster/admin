<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class USRPermission extends Model
{
	protected $table = 'usr_permissions';

    protected $fillable = [
        'name' ,
        'f_name' ,
        'url' ,
        'action'
    ];

    public function rolePermission()
    {
        return $this->hasMany("\App\USRRolePermissions" , "permission_id");
	}

    public function resource()
    {
        return $this->belongsTo("\App\USRResources");
	}

    public function actionType()
    {
        return $this->belongsTo("\App\USRActionType");
	}

    public function userPermission()
    {
        return $this->hasMany("\App\USRUserPermissions" ,"permission_id");
	}

    public function getRoles()
    {
        return $this->belongsToMany(USRRole::class , 'usr_role_permissions' , 'permission_id' , 'role_id');
    }
}