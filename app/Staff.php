<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Lumen\Http\Request;

class Staff extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = "staffs";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal',
        'address',
        'city',
        'pay_type_id',
        'mobile',
        'ice',
        'employeeid',
        'file',
        'tax_number',
        'nationality_id',
        'residence',
        'profileurl',
        'gender',
        'dob',
        'card_id',
        'role_id',
        'status_id',
        'company_id',
        'approver',


    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'postal',
        'address',
        'city',
        'pay_type_id',
        'ice',
        'employeeid',
        'file',
        'tax_number',
        'nationality_id',
        'residence',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function scopeByName($query, $name)
    {
        $query->where('firstname', $firstname)->orWhere('lastname', $name);
    }

    public function status()
    {
        return $this->belongsTo('App\Status');
    }

    public function jobs()
    {
        return $this->hasMany('App\Job');
    }


    public function permissions()
    {
        return $this->belongsToMany('App\Permission');
    }

    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    public function hasPermission(String $permission)
    {
        $permissions = $this->permissions;
        $permcount = count($permissions);
        for ($i = 0; $i < $permcount; $i++) {
            if ($permissions[$i]->name === $permission)
                return true;
        }

        return false;
    }

    public function hasStatus(String $status)
    {
        if ($this->status->name === $status)
            return true;
        return false;
    }

    public function training()
    {
        return $this->belongsToMany('App\Training');
    }

}