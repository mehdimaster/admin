<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class USRActionType extends Model
{
	protected $table = 'usr_action_type';

    public function permission()
    {
        return $this->hasMany("\App\USRPermission" , "action_type_id");
	}


}