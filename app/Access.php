<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    //

    protected $table = "access_levels";

    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];


    public function permissions(){
        return $this->belongsToMany('App\Permission');
    }

    public function branches(){
        return $this->belongsToMany('App\Branch');
    }

    public function groups(){
        return $this->belongsToMany('App\Group');
    }

    public function roles(){
        return $this->belongsToMany('App\Role');
    }
    
}
