<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dais extends Model
{
    protected $table = 'dais_info';
    protected $primaryKey = 'reg_id';
    protected $fillable = ['reg_id','conference_id', 'school_id', 'committee_id', 'position'];
    
    public function conference() {
        return $this->belongsTo('App\Conference');
    }

    public function committee()
    {
        return $this->belongsTo('App\Committee');
    }

    public function reg()
    {
        return $this->belongsTo('App\reg');
    }

    public function school()
    {
        return $this->belongsTo('App\School');
    }
}
