<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubProfile extends Model
{
    //

    public function user() {
        return $this->belongsTo('App\User');
    }

}
