<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    //

    protected $fillable = [
        'name',
        'description',
        'link',
        'frequency_id',
        'company_id',
    ];

    public function frequency()
    {
        return $this->belongsTo('App\Frequency');
    }

    public function files()
    {
        return $this->belongsToMany('App\Files');
    }

    public function staffs()
    {
        return $this->belongsToMany('App\Staff');
    }
}
