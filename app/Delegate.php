<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class Delegate extends Model
{
    protected $table='delegate_info';
    protected $primaryKey = 'user_id';
    protected $fillable = ['user_id','school_id','status','gender','sfz','grade','email','qq','wechat','partnername','parenttel','tel','committee_id','accomodate','roommatename','partner_user_id','roommate_user_id','notes'];

    public function committee() {
        return $this->belongsTo('App\Committee');
    }

    public function nation() {
        return $this->belongsTo('App\Nation');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function school() {
        return $this->belongsTo('App\School');
    }

    public function individual_assignments() {
        return $this->hasMany('App\Assignment');
    }

    public function nationgroups() {
        return $this->nation->nationgroups();
    }

    public function delegategroups() {
        return $this->belongstoMany('App\Delegategroup');
    }

    public function assignments() {
        $result = new Collection;
        if (isset($this->nation))
        {
            $nationgroups = $this->nationgroups;
            if (isset($nationgroups))
            {
                foreach($nationgroups as $nationgroup)
                {
                    $assignments = $nationgroup->assignments;
                    if (isset($assignments))
                        foreach ($assignments as $assignment)
                            $result->push($assignment);
                }
            }
        }
        $assignments = $this->committee->assignments;
        if (isset($assignments))
        {
            foreach ($assignments as $assignment)
                $result->push($assignment);
        }
        $delegategroups = $this->delegategroups;
        if (isset($delegategroups))
        {
            foreach($delegategroups as $delegategroup)
            {
                $assignments = $delegategroup->assignments;
                if (isset($assignments))
                    foreach ($assignments as $assignment)
                        $result->push($assignment);
            }
        }
        return $result->unique()->sortBy('id');
    }
    
    public function assignPartnerByName() 
    {
        $this->partner_user_id = null;
        if (isset($this->partnername))
        {
            $partner_name = $this->partnername;
            $partners = User::where('name', $partner_name);
            $count = $partners->count();
            if ($count == 0) 
            {
                if (isset($this->notes)) $this->notes .= "\n";
                $this->notes .= "未找到$partner_name" . "的报名记录！";
                $this->save();
                return $this->user->name . "\t\t搭档姓名$partner_name\t未找到搭档的报名记录";
            }
            if (!is_null($partners)) // 对于带空格的partnername值，在此if表达式外增加foreach表达式以逐一处理
            {
                //foreach ($partners as $partner)
                //{
                    $partner = $partners->first();
                    if ($partner->type != 'delegate') //continue;                        // 排除非代表搭档
                        return $this->user->name  ."\t".$partner->id . "\t搭档姓名$partner_name\t不是代表";
                    $delpartner = $partner->delegate;
                    if ($delpartner->committee != $this->committee) //continue;          // 排除非本委员会搭档
                        return $this->user->name  ."\t".$partner->id ."\t搭档姓名$partner_name\t不同委员会";
                    if (is_null($delpartner->partnername))                             // 如果对方未填搭档，自动补全
                        $delpartner->partnername = $this->user->name;
                    if ($delpartner->partnername != $this->user->name) //continue;       // 排除多角搭档
                        return $this->user->name  ."\t".$partner->id . "\t搭档姓名$partner_name\t多角搭档";
                    $this->partner_user_id = $partner->id;
                    $this->save();
                    $delpartner->partner_user_id = $this->user->id;
                    $delpartner->save();
                    return $this->user->name  ."\t".$partner->id . "\t搭档姓名$partner_name\t成功";
                //}
            }
	    if (isset($this->notes)) $this->notes .= "\n";
            $this->notes .= "在自动配对搭档时发生错误，请核查";
            $this->save();
            return;
        }
        return $this->user->name . "\t未填写搭档姓名";
    }
    
    public function partner() {
        return $this->belongsTo('App\User', 'partner_user_id'); 
    }
    
    public function assignRoommateByName() 
    {
        if (!$this->accomodate) return;
        $this->roommate_user_id = null;
        if (isset($this->roommatename))
        {
            $roommate_name = $this->roommatename;
            $roommates = User::where('name', $roommate_name);
            //$this->notes .= isset($roommates) ? '$roommates 非空' : '$roommates 空';
            $count = $roommates->count();
            if ($count == 0) 
            {
                if (isset($this->notes)) $this->notes .= "\n";
                $this->notes .= "未找到$roommate_name" . "的报名记录！";
                $this->save();
                return;
            }
            if (!is_null($roommates)) // 对于带空格的roommatename值，在此if表达式外增加foreach表达式以逐一处理
            {
                //foreach ($roommates as $roommate)
                //{
                    $roommate = $roommates->first();
                    if ($roommate->type == 'unregistered') return; //continue;                    // 排除未注册室友
                    $typedroommate = $roommate->specific();
                    if (is_null($typedroommate)) { $this->notes .= "specific null "; $this->save();}
                    if (is_null($typedroommate->roommatename))                          // 如果对方未填室友，自动补全
                        $typedroommate->roommatename = $this->user->name;
                    if ($typedroommate->roommatename != $this->user->name) {$this->notes .= "多角室友 "; $this->save(); return;}//continue;}    // 排除多角室友
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
                //}
            }
	    if (isset($this->notes)) $this->notes .= "\n";
            $this->notes .= "\$count=$count 在自动配对室友时发生错误，请核查";
            $this->save();
            return;
        }
    }
    
    public function roommate() {
        return $this->belongsTo('App\User', 'roommate_user_id'); 
    }
    
    public function documents() {
        $result = new Collection;
        if (isset($this->nation))
        {
            $nationgroups = $this->nationgroups;
            if (isset($nationgroups))
            {
                foreach($nationgroups as $nationgroup)
                {
                    $documents = $nationgroup->documents;
                    if (isset($documents))
                        foreach ($documents as $document)
                            $result->push($document);
                }
            }
        }
        $documents = $this->committee->documents;
        if (isset($documents))
        {
            foreach ($documents as $document)
                $result->push($document);
        }
        $delegategroups = $this->delegategroups;
        if (isset($delegategroups))
        {
            foreach($delegategroups as $delegategroup)
            {
                $documents = $delegategroup->documents;
                if (isset($documents))
                    foreach ($documents as $document)
                        $result->push($document);
            }
        }
        return $result->unique()->sortBy('id');
    }
}
