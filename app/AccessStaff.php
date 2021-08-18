<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    //

    protected $table = "access_staff";

    protected $fillable = [
        'access_id',
        'staff_id'
    ];


    public function staffs(){
        return $this->hasMany('App\Staff','staff_id');
    }

    public function access(){
        return $this->hasMany('App\Access','access_id');
    }
    
}
