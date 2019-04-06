<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class USRResources extends Model
{
	protected $table = 'usr_resources';

    public function permission()
    {
        return $this->hasMany("\App\USRPermission" ,"resource_id");
	}

}