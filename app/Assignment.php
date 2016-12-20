<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $table='assignments';
    protected $fillable = ['nationgroup_id', 'subject_type', 'handin_type', 'title', 'description', 'deadline'];    

    public function nationgroup() 
    {
        return $this->belongsTo('App\Nationgroup');
    }

    public function committee() 
    {
        return $this->belongsTo('App\Nationgroup')->hasMany('App\Nation')->belongsTo('App\Committee');
    }

    public function handins()
    {
	return $this->hasMany('App\Handin');
    }        
	
	// TODO: ��������Ĺ�����ϵ������л���Ҫ��
}
