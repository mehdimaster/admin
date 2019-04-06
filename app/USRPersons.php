<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class USRPersons extends Model
{
    use SoftDeletes;
	protected $table = 'usr_persons';

    protected $fillable = [
        'name' ,
        'family' ,
        'dob' ,
        'national_code',
        'gender_id',
        'address'
    ];

    protected $appends = ['fullName'];

    protected static function boot() {
        parent::boot();

        static::deleted(function ($person) {
            $person->user()->delete();
        });
    }

    public function getFullNameAttribute()
    {
        $final = null;
        if ($this->name && $this->family) {
            $final = $this->name .' '. $this->family ;
        } else {
            $final = 'نام نامشخص';
        }

        return $final;
    }

    public function user()
    {
        return $this->hasMany(User::class , 'person_id');
	}

    public function oneUser()
    {
        return $this->hasOne(User::class , 'person_id');
    }

}