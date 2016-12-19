<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $fillable = array('name');

    public function delegates() {
        return $this->hasMany('App\Delegate');
    }

}
