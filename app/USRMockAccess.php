<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class USRMockAccess extends Model
{
    use SoftDeletes;

    protected $table = 'use_access_data_mock';

    protected $fillable = [
        'user_id',
        'roles' ,
        'permissions' ,
        'denialPermissions'
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
	}

}