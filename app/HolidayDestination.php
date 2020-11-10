<?php

namespace App;

use Eloquent;

class HolidayDestination extends Eloquent {

    protected $fillable = ['place_name'
    ];
    protected $primaryKey = 'id';
    protected $table = 'holiday_destination';

    /*public function getDestination() {
        return $this->hasMany('App\MstMediaItemLocation');
    }*/
}
