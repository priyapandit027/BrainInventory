<?php

namespace App;

use Eloquent;

class UserRating extends Eloquent {

    protected $fillable = ['user_id','destination_id','rating','review'
    ];
    protected $primaryKey = 'id';
    protected $table = 'user_rating';

    /*public function getDestination() {
        return $this->hasMany('App\MstMediaItemLocation');
    }*/
}
