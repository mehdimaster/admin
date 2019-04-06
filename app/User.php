<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Traits\HasRoles;
use Plugin;

class User extends Authenticatable
{
    use HasRoles;
    use SoftDeletes;

    protected $guard_name = 'web';
    protected $table = 'users';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'email',
        'mobile',
        'person_id',
        'create_user_id',
        'update_user_id',
        'password',
        'avatar',
        'active',
        'user_status'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $appends = ['fullAvatar', 'first_name', 'last_name'];

    public function getFirstNameAttribute()
    {
        $loc = App::getLocale();
        $final = null;

        if ($loc == 'fa') {
            if ($this->person && $this->person->name) {
                $final = $this->person->name;
            }
        } else {
            if ($this->person && $this->person->en_name) {
                $final = $this->person->en_name;
            }
        }
        return $final;
    }

    public function getLastNameAttribute()
    {
        $loc = App::getLocale();
        $final = null;

        if ($loc == 'fa') {
            if ($this->person && $this->person->family) {
                $final = $this->person->family;
            }
        } else {
            if ($this->person && $this->person->en_family) {
                $final = $this->person->en_family;
            }
        }
        return $final;
    }

    public function getFullAvatarAttribute()
    {

        $final = null;
        if ($this->avatar) {
            $final = asset('pictures/avatar/'.$this->avatar);
            if (file_exists('pictures/avatar/'.$this->avatar)) {
                return $final;
            } else {
                return asset('pictures/avatar/defaultAvatar/user-pic.png');
            }
        } else {
            return asset('pictures/avatar/defaultAvatar/user-pic.png');
        }
    }

    public function person()
    {
        return $this->belongsTo('\App\USRPersons');
    }

    public function roleUser()
    {
        return $this->hasMany("\App\USRRoleUsers", "user_id");
    }

    public function userPermission()
    {
        return $this->hasMany("\App\USRUserPermissions", "user_id");
    }

    public function getRoles()
    {
        return $this->belongsToMany(USRRole::class, 'usr_model_has_roles', 'model_id', 'role_id');
    }

    public function mockAccess()
    {
        return $this->hasOne(USRMockAccess::class, 'user_id');
    }


//    public function userGroup()
//    {
//        return $this->hasOne('\App\UserGroup', 'user_id');
//    }

    public function userRequestPassword()
    {
        return $this->hasMany('\App\UserRequestPassword', 'user_id');
    }

    public function userExtra()
    {
        return $this->hasMany('\App\UserExtra', 'extra_id');
    }

    public function payment()
    {
        return $this->hasMany('\App\Payment', 'user_id');
    }

    public function preReserveXml()
    {
        return $this->hasMany('\App\PreReserveXml', 'user_id');
    }

    public function flightReference()
    {
        return $this->hasMany('\App\FlightReference', 'user_id');
    }

    public function flightHistory()
    {
        return $this->hasMany('\App\FlightHistory', 'user_id');
    }

    public function newsLetterSubscriber()
    {
        return $this->hasOne('\App\NewsLetterSubscriber', 'user_id');
    }

    public function flightTraveller()
    {
        return $this->hasMany('\App\FlightTraveller', 'user_id');
    }

    public function hotelPassenger()
    {
        return $this->hasMany('\App\HotelPassenger', 'user_id');
    }

    public function hotelHistory()
    {
        return $this->hasMany('\App\HotelHistory', 'user_id');
    }

    public function flightPassenger()
    {
        return $this->hasMany('\App\FlightPassenger', 'user_id');
    }

    public function order()
    {
        return $this->hasMany('\App\Order', 'user_id');
    }

    public function traveller()
    {
        return $this->hasMany('\App\Traveller', 'user_id');
    }

    public function lastTraveller()
    {
        return $this->hasOne(Traveller::class, 'user_id')->where('active', 1)->latest();
    }

    public function userPerson()
    {
        return $this->belongsTo('App\USRPersons', 'person_id');

    }

    public function group()
    {
        return $this->belongsToMany('App\MarkupGroup');
    }

    public function optionalSetting()
    {
        return $this->hasOne('App\UserOptionalSetting','user_id','id');
    }

}
