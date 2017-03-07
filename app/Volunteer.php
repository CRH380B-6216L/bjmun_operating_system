<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    protected $table='volunteer_info';
    protected $primaryKey = 'user_id';
    protected $fillable = ['user_id','conference_id','school_id','status'];

    public function reg() {
        return $this->belongsTo('App\Reg');
    }

    public function school() {
        return $this->belongsTo('App\School');
    }

    public function conference() {
        return $this->belongsTo('App\Conference');
    }
}
