<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Observer extends Model
{
    protected $table='observer_info';
    protected $primaryKey = 'user_id';
    protected $fillable = ['user_id','school_id','status','gender','sfz','grade','email','qq','wechat','parenttel','tel','accomodate','roommatename','roommate_user_id','notes'];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function school() {
        return $this->belongsTo('App\School');
    }
    
    public function assignroommateByName() 
    {
        $this->roommate_user_id = null;
        if (isset($this->roommatename))
        {
            $roommate_name = $this->roommatename;
            $roommates = User::where('name', $roommate_name);
            if (!is_null($roommates)) // 对于带空格的roommatename值，在此if表达式外增加foreach表达式以逐一处理
            {
                foreach ($roommates as $roommate)
                {
                    if ($roommate->type == 'unregistered') continue;                    // 排除未注册室友
                    $typedroommate = $roommate->specific();
                    if (is_null($typedroommate->roommatename))                          // 如果对方未填室友，自动补全
                        $typedroommate->roommatename = $this->user->name;
                    if ($typedroommate->roommatename != $this->user->name) continue;    // 排除多角室友
                    if ($typedroommate->gender != $this->gender)                        // 排除男女混宿
                    {
                        if (isset($this->notes)) $this->notes .= "\n";
                        $this->notes .= "在自动配对室友时检测到室友为异性，请核查";
                        $this->save();
                        return;
                    }
                    $this->roommate_user_id = $roommate->id;       
                    $this->save();
                    $typedroommate->roommate_user_id = $this->user->id;
                    $typedroommate->save();
                    return;
                }
            }
	    if (isset($this->notes)) $this->notes .= "\n";
            $this->notes .= "在自动配对室友时发生错误，请核查";
            $this->save();
            return;
        }
    }
    
    public function roommate() {
        return $this->hasOne('App\User', 'roommate_user_id'); 
    }
}
