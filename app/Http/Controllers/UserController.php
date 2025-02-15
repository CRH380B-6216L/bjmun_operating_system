<?php
/**
 * Copyright (C) MUNPANEL
 * This file is part of MUNPANEL System.
 *
 * Open-sourced under AGPL v3 License.
 */

namespace App\Http\Controllers;

use Config;
use App\User;
use App\Reg;
use App\Delegate;
use App\Volunteer;
use App\Observer;
use App\School;
use App\Committee;
use App\Permission;
use App\Orgteam;
use App\Role;
use App\Interviewer;
use App\Assignment;
use App\Delegategroup;
use App\Conference;
use App\Card;
use App\Dais;
use App\Good;
use App\Order;
use App\Note;
use App\Nation;
use App\Teamadmin;
use App\Option;
use App\Nationgroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'test']);
    }

    /**
     * (Deprecated) Save delegate registration form.
     *
     * @param Request $request
     * return void
     */
    public function regSaveDel(Request $request)
    {
        //$user = Auth::user();
        $user = User::find($request->id);
        if (is_null($request->id))
            $user = Auth::user();
        else if (Reg::current()->type == 'ot' && (!Reg::current()->can('edit-regs')))
            return "error";
        else if (Reg::current()->type != 'ot' && Reg::current()->type != 'school')
            return "error";
        else if (Reg::current()->type == 'school' && Reg::current()->school->id != $user->specific()->school->id)
            return "error";
        $user->type = 'delegate';
        $user->save();
        $del = $user->delegate;
        if (is_null($del))
        {
            $del = new Delegate;
            $del->user_id = $user->id;
            $del->email = $user->email;
        }
        if (Reg::current()->type != 'school')
            $del->school_id = $request->school;
        else
            $del->school_id = Reg::current()->school->id;
        if (Reg::current()->type != 'ot' || $del->status == null)
            $del->status = 'reg';
        $del->gender = $request->gender;
        $del->sfz = $request->sfz;
        $del->grade = $request->grade;
        $del->qq = $request->qq;
        $del->wechat = $request->wechat;
        $del->partnername = $request->partnername;
        $del->parenttel = $request->parenttel;
        $del->tel = $request->tel;
        $del->committee_id = $request->committee;
        $del->accomodate = $request->accomodate;
        $del->roommatename = $request->roommatename;
        $del->save();
        Volunteer::destroy($user->id);
        Observer::destroy($user->id);
    }

    /**
     * (Deprecated) Save volunteer registration form.
     *
     * @param Request $request
     * return void
     */
    public function regSaveVol(Request $request)
    {
        //$user = Auth::user();
        $user = User::find($request->id);
        if (is_null($request->id))
            $user = Auth::user();
        else if (Reg::current()->type == 'ot' && (!Reg::current()->can('edit-regs')))
            return "error";
        else if (Reg::current()->type != 'ot' && Reg::current()->type != 'school')
            return "error";
        else if (Reg::current()->type == 'school' && Reg::current()->school->id != $user->specific()->school->id)
            return "error";
        $user->type = 'volunteer';
        $user->save();
        $vol = $user->volunteer;
        if (is_null($vol))
        {
            $vol = new Volunteer;
            $vol->user_id = $user->id;
            $vol->email = $user->email;
        }
        if (Reg::current()->type != 'school')
            $vol->school_id = $request->school;
        else
            $vol->school_id = Reg::current()->school->id;
        if (Reg::current()->type != 'ot' || $vol->status == null)
            $vol->status = 'reg';
        $vol->gender = $request->gender;
        $vol->sfz = $request->sfz;
        $vol->grade = $request->grade;
        $vol->qq = $request->qq;
        $vol->wechat = $request->wechat;
        $vol->parenttel = $request->parenttel;
        $vol->tel = $request->tel;
        $vol->accomodate = $request->accomodate;
        $vol->roommatename = $request->roommatename;
        $vol->save();
        Delegate::destroy($user->id);
        Observer::destroy($user->id);
    }

    /**
     * (Deprecated) Save observer registration form.
     *
     * @param Request $request
     * return void
     */
    public function regSaveObs(Request $request)
    {
        //$user = Auth::user();
        $user = User::find($request->id);
        if (is_null($request->id))
            $user = Auth::user();
        else if (Reg::current()->type == 'ot' && (!Reg::current()->can('edit-regs')))
            return "error";
        else if (Reg::current()->type != 'ot' && Reg::current()->type != 'school')
            return "error";
        else if (Reg::current()->type == 'school' && Reg::current()->school->id != $user->specific()->school->id)
            return "error";
        $user->type = 'observer';
        $user->save();
        $obs = $user->observer;
        if (is_null($obs))
        {
            $obs = new Observer;
            $obs->user_id = $user->id;
            $obs->email = $user->email;
        }
        $obs->school_id = $request->school;
        if (Reg::current()->type != 'ot' || $obs->status == null)
            $obs->status = 'reg';
        $obs->gender = $request->gender;
        $obs->sfz = $request->sfz;
        $obs->grade = $request->grade;
        $obs->qq = $request->qq;
        $obs->wechat = $request->wechat;
        $obs->parenttel = $request->parenttel;
        $obs->tel = $request->tel;
        $obs->accomodate = $request->accomodate;
        $obs->roommatename = $request->roommatename;
        $obs->save();
        Delegate::destroy($user->id);
        Volunteer::destroy($user->id);
    }

    /**
     * Save registration form (dynamic).
     *
     * @param Request $request
     * @return void
     */
    public function reg2(Request $request)
    {
        if (!validateRegDate($request->type))
            return view('error', ['msg' => '报名类型不匹配！']);
        $customTable = json_decode(Reg::currentConference()->option('reg_tables'))->regTable; //todo: table id
        if ($request->type == 'dais')
            $customTable = json_decode(Reg::currentConference()->option('reg_tables'))->daisregTable; //todo: table id
        if (!Auth::check())
        {
            if (is_object(User::where('email', $request->email)->first()))
            {
                //To-Do: error! 建议用 JS 判断
            }
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password2);
            $user->type = 'unregistered';
            $user->save();
        }
        else
            $user = Auth::user();
        $conf = $request->conference_id;
        $reg = Reg::find($request->reg_id);
        $reg->user_id = $user->id;
        $reg->conference_id = $conf;
        $reg->type = $request->type;
        $reg->enabled = true;
        $reg->gender = $request->gender;
        //if (!empty($request->committee))
        $regInfo = new \stdClass();
        $personal_info = new \stdClass();
        if (!empty($request->dateofbirth))
            $personal_info->dateofbirth = $request->dateofbirth;
        if (!empty($request->province))
            $personal_info->province = $request->province;
        $personal_info->school = $request->school;
        //$school = School::where('name', $request->school)->first();
        //if (!empty($school)) $reg->school_id = $school->id;
        $personal_info->yearGraduate = $request->yearGraduate;
        if (!empty($request->sfz))
        {
            $personal_info->typeDocument = $request->typeDocument;
            $personal_info->sfz = $request->sfz;
        }
        $personal_info->tel = $request->tel;
        if (!empty($request->tel2))
            $personal_info->tel2 = $request->tel2;
        if (!empty($request->qq))
            $personal_info->qq = $request->qq;
        if (!empty($request->skype))
            $personal_info->skype = $request->skype;
        if (!empty($request->wechat))
            $personal_info->wechat = $request->wechat;
        if (!empty($request->parentname))
        {
            $personal_info->parentname = $request->parentname;
            $personal_info->parentrelation = $request->parentrelation;
            $personal_info->parenttel = $request->parenttel;
        }
        $regInfo->personinfo = $personal_info;
        if (isset($customTable->experience) && in_array($reg->type, $customTable->experience->uses))
        {
            $experience = new \stdClass();
            $experience->startYear = $request->startYear;
            $items = array();
            // TODO: 加载 MUNPANEL 收录会议的参会经历
            if (in_array($reg->type, $customTable->experience->custom))
            {
                if (!empty($request->level1) && !empty($request->date1) && !empty($request->name1) && !empty($request->role1))
                {
                    $expitem = new \stdClass();
                    $expitem->level = $request->level1;
                    $expitem->dates = $request->date1;
                    $expitem->name = $request->name1;
                    $expitem->role = $request->role1;
                    if (!empty($request->award1)) $expitem->award = $request->award1;
                    if (!empty($request->others1)) $expitem->others = $request->others1;
                    array_push($items, $expitem);
                }
                if (!(empty($request->level2) || empty($request->date2) || empty($request->name2) || empty($request->role2)))
                {
                    $expitem = new \stdClass();
                    $expitem->level = $request->level2;
                    $expitem->dates = $request->date2;
                    $expitem->name = $request->name2;
                    $expitem->role = $request->role2;
                    if (!empty($request->award2)) $expitem->award = $request->award2;
                    if (!empty($request->others2)) $expitem->others = $request->others2;
                    array_push($items, $expitem);
                }
                if (!(empty($request->level3) || empty($request->date3) || empty($request->name3) || empty($request->role3)))
                {
                    $expitem = new \stdClass();
                    $expitem->level = $request->level3;
                    $expitem->dates = $request->date3;
                    $expitem->name = $request->name3;
                    $expitem->role = $request->role3;
                    if (!empty($request->award3)) $expitem->award = $request->award3;
                    if (!empty($request->others3)) $expitem->others = $request->others3;
                    array_push($items, $expitem);
                }
            }
            $experience->item = $items;
            $regInfo->experience = $experience;
        }
        $conf_info = new \stdClass();
        foreach ($customTable->conference->items as $item)
        {
            if (isset($item->name) && !empty($request->{$item->name}))
                $conf_info->{$item->name} = $request->{$item->name};
            else
            {
                switch ($item->type)
                {
                    case 'preCommittee':
                        $conf_info->committee = $request->committee;
                    break;
                    case 'prePartnerName':
                        $conf_info->partnername = $request->partnername;
                    break;
                    case 'preIsAccomodate':
                        $reg->accomodate = $request->accomodate;
                    break;
                    case 'preRoommateName':
                        $conf_info->roommatename = $request->roommatename;
                    break;
                    case 'preGroupOptions':
                        $conf_info->groupOption = $request->groupOption;
                    break;
                    case 'preRemarks':
                        $conf_info->remarks = $request->remarks;
                    break;
                    case 'group':
                        foreach ($item->items as $subitem)
                            if (isset($subitem->name) && !empty($request->{$subitem->name}))
                                $conf_info->{$subitem->name} = $request->{$subitem->name};
                    break;
                }
            }
        }
        if (isset($customTable->targets))
        {
            $targets = (array)$customTable->targets;
            foreach ($targets as $key => $item)
            {
                switch ($key)
                {
                    case 'committee':
                    if (!empty($request->{$targets['committee']}))
                        $conf_info->committee = $request->{$targets['committee']};
                    break;
                }
            }
        }
        // 校验 committee 是否非空
        if ($reg->type == 'delegate' && is_null($conf_info->committee))
            return view('error', ['msg' => '您提交的报名信息似乎有问题，请再试一次。']);
        if ($reg->type == 'dais' && is_null($conf_info->language))
            return view('error', ['msg' => '您提交的申请信息似乎有问题，请再试一次。']);
        $regInfo->conference = $conf_info;
        $regInfo->reg_at = date('Y-m-d H:i:s');
        $reg->reginfo = json_encode($regInfo);
        $reg->save();
        $reg->make();
        if (isset($customTable->targets))
        foreach ($customTable->actions as $element)
        {
            if (empty($request->{$element->item})) continue;
            switch ($element->action)
            {
                case 'assignDelGroup':
                    if ($reg->type != 'delegate') continue 2;
                    // TODO: 调试 bindDelegategroup 并替换现在方法
                    $dg = Delegategroup::find($request->{$element->item});
                    // $dg = Committee::findOrFail($conf_info->committee)->bindDelegategroup;
                    if (is_null($dg)) continue 2;
                    $dg->delegates()->syncWithoutDetaching($reg->id);
                break;
            }
        }
        $reg->addEvent('registration_submitted', '');
        return redirect('/home');
    }

    public function setAccomodation(Request $request)
    {
        if (Reg::currentID() != $request->reg_id && Reg::current()->type != 'ot')
            return 'error';
        $reg = Reg::findOrFail($request->reg_id);
        $reg->accomodate = $request->accomodate;
        $reg->updateInfo('conference.roommatename', $request->roommatename);
        $reg->save();
        if ((!isset($reg->order_id)) && Reg::currentConference()->option('reg_order_create_time') == 'seatLock' && $reg->specific()->seat_locked)
            $reg->createConfOrder();
        elseif ((!isset($reg->order_id)) && Reg::currentConference()->option('reg_order_create_time') == 'oVerify' && $reg->specific()->status == 'oVerified')
            $reg->createConfOrder();
        return redirect('/home');
    }
    
    /**
     * make a registration ot verified.
     *
     * @param int $id the id of the registration
     * @return void
     */
    public function oVerify($id)
    {
        if (Reg::current()->type != 'ot' || (!Reg::current()->can('approve-regs')))
            return "您无权执行该操作！";
        $reg = Reg::findOrFail($id);
        $specific = $reg->specific();
        if ($specific->status != 'sVerified')
            return "无法为此报名者执行该操作！";
        $specific->status = $specific->nextStatus();
        $specific->save();
        $reg->addEvent('ot_verification_passed', '{"name":"'.Reg::current()->name().'"}');
        if ((!isset($reg->order_id)) && Reg::currentConference()->option('reg_order_create_time') == 'oVerify' && isset($reg->accomodate))
            $reg->createConfOrder();
        return 'success';
        return redirect('/regManage?initialReg='.$id);
    }

    /**
     * make a registration ot not verified.
     *
     * @param int $id the id of the registration
     * @return void
     */
    public function oNoVerify($id)
    {
        if (Reg::current()->type != 'ot' || (!Reg::current()->can('approve-regs')))
            return "您无权执行该操作！";
        $reg = Reg::findOrFail($id);
        $specific = $reg->specific();
        if ($specific->status != 'sVerified')
            return "无法为此报名者执行该操作！";
        $specific->status = 'fail';
        $specific->save();
        $reg->addEvent('ot_verification_rejected', '{"name":"'.Reg::current()->name().'"}');
        return 'success';
        //return redirect('/regManage?initialReg='.$id);
    }

    /**
     * make a registration ot not verified.
     *
     * @param int $id the id of the registration
     * @return void
     */
    public function oReVerify($id)
    {
        if (Reg::current()->type != 'ot' || (!Reg::current()->can('approve-regs')))
            return "您无权执行该操作！";
        $reg = Reg::findOrFail($id);
        $specific = $reg->specific();
        if ($specific->status != 'fail')
            return "无法为此报名者执行该操作！";
        $specific->status = 'sVerified';
        $specific->save();
        //$reg->addEvent('ot_verification_rejected', '{"name":"'.Reg::current()->name().'"}');
        //TODO: event
        return 'success';
        //return redirect('/regManage?initialReg='.$id);
    }

    /**
     * make a registration school verified.
     *
     * @param int $id the id of the registration
     * @return void
     */
    public function schoolVerify($id)
    {
        $reg = Reg::findOrFail($id);
        if ($reg->school_id != Reg::current()->school_id)
            return "error";
        $specific = $reg->specific();
        $specific->status = 'sVerified';
        $reg->addEvent('school_verification_passed', '{"name":"'.Reg::current()->name().'"}');
        $specific->save();
    }

    /**
     * make a registration school unverified.
     *
     * @param int $id the id of the registration
     * @return void
     */
    public function schoolUnverify($id)
    {
        $reg = Reg::findOrFail($id);
        if ($reg->school_id != Reg::current()->school_id)
            return "error";
        $specific = $reg->specific();
        $specific->status = 'reg';
        $specific->save();
    }

    /**
     * set committee for a single delegate.
     *
     * @param Request $request
     * @return void
     */
    public function setCommittee(Request $request)
    {
        if (Reg::current()->type != 'ot')
            return "您无权执行该操作！";
        $reg = Reg::findOrFail($request->reg_id);
        $specific = $reg->specific();
        if ($reg->type != 'delegate' || $specific->status == 'fail' || $reg->conference_id != Reg::currentConferenceID())
            return "无法为此报名者执行该操作！";
        if ($specific->seat_locked)
            return "请先解锁席位！";

        $committee = Committee::find($request->committee);
        if ((!isset($committee)) || $committee->conference_id != Reg::currentConferenceID())
            return '委员会不存在或不属于本会议！';
        if ($specific->committee_id == $request->committee)
            return '无需移动';

        $specific->committee_id = $request->committee;
        $specific->save();
        $reg->addEvent('committee_moved', '{"name":"'.Reg::current()->name().'", "committee":"'.Committee::findOrFail($request->committee)->display_name.'"}');
        if (isset($request->partner) && isset($specific->partner))
        {
            $partner = $specific->partner;
            if ($request->partner == 'moveall' && $committee->is_dual)
            {
                $partner->committee_id = $request->committee;
                $partner->save();
                $reg->addEvent('committee_moved', '{"name":"'.Reg::current()->name().'", "committee":"'.Committee::findOrFail($request->committee)->display_name.'"}');
                return '已成功变更委员会，并将搭档所属委员会一并变更';
            }
            $partner->partner_reg_id = null;
            $specific->partner_reg_id = null;
            return '已成功变更委员会，并解除搭档配对';
        }
        //return redirect('/regManage?initialReg='.$request->id);
        return 'success';
    }

    public function setPairing(Request $request)
    {
        if (Reg::current()->type != 'ot')
            return "您无权执行该操作！";
        $reg = Reg::findOrFail($request->reg_id);
        if ($request->type == 'partner')
            return $reg->delegate->assignPartnerByRid($request->other_id, true);
        else if ($request->type == 'roommate')
            return $reg->assignRoommateByRid($request->other_id, true);
    }

    /**
     * set delegate group for a single delegate.
     *
     * @param Request $request
     * @return void
     */
    public function setDelGroup(Request $request)
    {
        if (Reg::current()->type != 'ot')
            return "您无权执行该操作！";
        $reg = Reg::find($request->id);
        $specific = $reg->specific();
        if ($reg->type != 'delegate' || $specific->status == 'fail')
            return "无法为此报名者执行该操作！";
        $specific->delegategroups()->sync($request->delgroup);
        //return redirect('/regManage?initialReg='.$request->id);
        return 'success';
    }

    /**
     * set a registration to a certain status.
     *
     * @param int $id the id of the registration
     * @param string $status the new status of the registration
     * @return void
     */
    public function setStatus($id, $status)
    {
        if (Reg::current()->type != 'ot' || (!Reg::current()->can('approve-regs')))
            return "Error";
        if ($status == 'paid' && (!Reg::current()->can('approve-regs-pay')))
            return "Error";
        $specific =  User::find($id)->specific();
        $specific->status = $status;
        $specific->save();
    }

    /**
     * Register school accounts from csv file.
     *
     * @return string registration result
     */
    public function regSchool()
    {
        if (($handle = fopen("/var/www/munpanel/test.csv", "r")) !== FALSE) {
            $resp = "";
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    $resp = $resp. $data[$c] . "<br />\n";
                }
                $user = new User;
                $user->name = $data[0];
                $user->password = Hash::make($data[1]);
                $user->email = $data[0]. '@schools.bjmun.org';
                $user->type = 'school';
                $user->save();
                $school = School::where('name', $data[0])->first();
                $school->user_id =$user->id;
                $school->save();
                $resp = $resp. response()->json($user) . "<br />\n";
                $resp = $resp. response()->json($school) . "<br />\n";
            }
            fclose($handle);
            return $resp;
        }
    }

    /**
     * Register dais accounts from csv file.
     *
     * @return string registration result
     */
    public function regDais()
    {
         if (($handle = fopen("/var/www/munpanel/test.csv", "r")) !== FALSE) {
            $resp = "";
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    $resp = $resp. $data[$c] . "<br />\n";
                }
                $user = User::firstOrNew(['email' => $data[1]]);
                $user->name = $data[0];
                $user->password = Hash::make(strtok($data[1], '@') . '2017');
                $resp = $resp. 'pwd: ' . strtok($data[1], '@') . '2017' ."<br/>\n";
                $user->email = $data[1];
                Delegate::destroy($user->id);
                Volunteer::destroy($user->id);
                Observer::destroy($user->id);
                $user->type = 'dais';
                $user->save();
                $dais = new Dais;
                $dais->user_id = $user->id;
                $dais->committee_id = Committee::where('name', '=', $data[2])->first()->id;
                $dais->position = 'dm';
                $dais->school_id = School::firstOrCreate(['name' => $data[3]])->id;
                $dais->save();
                $resp = $resp. response()->json($user) . "<br />\n";
                $resp = $resp. response()->json($dais) . "<br />\n";
            }
            fclose($handle);
            return $resp;
        }
    }

    /**
     * Change the password of the logged in user.
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function doChangePwd(Request $request)
    {
        $user = Auth::user();
        if (Hash::check($request->oldPassword, $user->password))
        {
            $user->password = Hash::make($request->newPassword);
            $user->save();
            return redirect(mp_url('/home'));
        }
        else
            return view('error', ['msg' => 'Wrong password!']);
    }

    /**
     * Update a property of a user.
     *
     * @param Request $request
     * @param int $id the id of the user to be updated
     * @return void
     */
    public function updateUser(Request $request, $id)
    {
        if (Reg::current()->type != 'ot')
            return 'Error';
        $user = User::findOrFail($id);
        $name = $request->get('name');
        $value = $request->get('value');
        if ($name == 'password')
            $value = Hash::make($value);
        if ($name == 'type')
        {
            Delegate::destroy($user->id);
            Volunteer::destroy($user->id);
            Observer::destroy($user->id);
        }
        $user->$name = $value;
        $user->save();
    }

    /**
     * Update a property of a school.
     *
     * @param Request $request
     * @param int $id the id of the school to be updated
     * @return void
     */
    public function updateSchool(Request $request, $id)
    {
        if (Reg::current()->type != 'ot')
            return 'Error';
        $school = School::findOrFail($id);
        $name = $request->get('name');
        $value = $request->get('value');
        $school->$name = $value;
        $school->save();
    }

    /**
     * Update a property of a committee.
     *
     * @param Request $request
     * @param int $id the id of the committee to be updated
     * @return void
     */
    public function updateCommittee(Request $request, $id)
    {
        if (Reg::current()->type != 'ot')
            return 'Error';
        $committee = Committee::findOrFail($id);
        $name = $request->get('name');
        $value = $request->get('value');
        $committee->$name = $value;
        $committee->save();
    }

    /**
     * Delete a user from database.
     *
     * @param Request $request
     * @param int $id the id of the user to be deleted
     * @return void
     */
    public function deleteUser(Request $request, $id)
    {
        if (Reg::current()->type != 'ot')
            return 'Error';
        Reg::destroy($id);
    }

    /**
     * Delete a committee from database.
     *
     * @param Request $request
     * @param int $id the id of the committee to be deleted
     * @return void
     */
    public function deleteCommittee(Request $request, $id)
    {
        // TODO: 危险！！！！！尽快改用 Soft deleteing ！！！！
        if (Reg::current()->type != 'ot')
            return 'Error';
        Committee::destroy($id);
    }

    /**
     * Delete a school from database.
     *
     * @param Request $request
     * @param int $id the id of the school to be deleted
     * @return void
     */
    public function deleteSchool(Request $request, $id)
    {
        if (Reg::current()->type != 'ot')
            return 'Error';
        School::destroy($id);
    }

    /**
     * Initiate permissions to the database.
     */
    public function createPermissions()
    {
        /*$editUser = new Permission();
        $editUser->name = 'edit-users';
        $editUser->display_name = '用户管理';
        $editUser->description = '添加、删除、编辑用户的登陆信息、权限';
        $editUser->save();

        $editRole = new Permission();
        $editRole->name = 'edit-roles';
        $editRole->display_name = '角色管理';
        $editRole->description = '添加、删除角色，修改角色所含权限';
        $editRole->save();

        $viewReg = new Permission();
        $viewReg->name = 'view-regs';
        $viewReg->display_name = '报名信息查看';
        $viewReg->description = '查看代表、志愿者、观察员的报名信息';
        $viewReg->save();

        $editReg = new Permission();
        $editReg->name = 'edit-regs';
        $editReg->display_name = '报名信息编辑';
        $editReg->description = "编辑代表、志愿者、观察员的报名信息";
        $editReg->save();

        $approveReg = new Permission();
        $approveReg->name = 'approve-regs';
        $approveReg->display_name = '报名信息审核';
        $approveReg->description = '修改代表、志愿者、观察员的报名状态（不能修改为已缴费）';
        $approveReg->save();

        $approvePay = new Permission();
        $approvePay->name = 'approve-regs-pay';
        $approvePay->display_name = '报名缴费审核';
        $approvePay->description = '修改代表、志愿者、观察员的报名状态为已缴费（需要拥有报名信息审核权限）';
        $approvePay->save();

        $editCom = new Permission();
        $editCom->name = 'edit-committees';
        $editCom->display_name = '委员会管理';
        $editCom->description = '添加、删除、编辑委员会';
        $editCom->save();

        $editSchool = new Permission();
        $editSchool->name = 'edit-schools';
        $editSchool->display_name = '学校管理';
        $editSchool->description = '添加、删除、编辑学校';
        $editSchool->save();

        $assignInterview = new Permission();
        $assignInterview->name = 'assign-interviews';
        $assignInterview->display_name = '面试管理';
        $assignInterview->description = '面试代表、填写反馈、评价及打分';
        $assignInterview->save();

        $assignRole = new Permission();
        $assignRole->name = 'assign-roles';
        $assignRole->display_name = '席位分配';
        $assignRole->description = '为代表分配席位';
        $assignRole->save();

        $viewPermission = new Permission();
        $viewPermission->name = 'view-pwemissions';
        $viewPermission->display_name = '权限查看';
        $viewPermission->description = '查看团队成员的具体权限';
        $viewPermission->save();

        $editStore = new Permission();
        $editStore->name = 'edit-store';
        $editStore->display_name = '纪念品商店管理';
        $editStore->description = '管理纪念品商店及确认代表交易';
        $editStore->save();

        $editUser = Permission::find(1);
        $editRole = Permission::find(2);
        $viewReg = Permission::find(3);
        $editReg = Permission::find(4);
        $approveReg = Permission::find(5);
        $approvePay = Permission::find(6);
        $editCom = Permission::find(7);
        $editSchool = Permission::find(8);
        $editNation = Permission::find(9);

        $editReg = new Permission();
        $editReg->name = 'edit-ot';
        $editReg->display_name = '会议团队成员管理';
        $editReg->description = "增删、编辑、管理会议组织团队和学术团队成员";
        $editReg->save();

        /*$sysadmin = new Role();
        $sysadmin->name = 'sysadmin';
        $sysadmin->display_name = '系统管理员';
        $sysadmin->description = '包括所有权限。一般不应使用此角色而应使用若干子角色结合。';
        $sysadmin->save();

        //$sysadmin = Role::find(1);

        //$sysadmin->attachPermissions(array($editUser, $editRole, $viewReg, $editReg, $approveReg, $approvePay, $editCom, $editSchool));
        //User::where('email', '=', 'yixuan@bjmun.org')->first()->attachRole($sysadmin);

        $coreteam = new Role();
        $coreteam->name = 'coreteam';
        $coreteam->display_name = '核心组';
        $coreteam->description = '包括除更改用户信息以外的所有权限，并可查看团队其他成员的权限。';
        $coreteam->save();
        $coreteam->attachPermissions(array($editRole, $viewReg, $editReg, $approveReg, $approvePay, $editCom, $editSchool, $editNation, $editInterview, $assignRole, $viewPermission, $editStore));

        $miscteam = new Role();
        $miscteam->name = 'miscteam';
        $miscteam->display_name = '会务组';
        $miscteam->description = '包括查看、审核和更改报名信息的权限。';
        $miscteam->save();
        $miscteam->attachPermissions(array($viewReg, $editReg, $approveReg, $editSchool, $editStore));

        $interviewadmin = new Role();
        $interviewadmin->name = 'interviewadmin';
        $interviewadmin->display_name = '面试协理组';
        $interviewadmin->description = '包括审核报名信息、编辑委员会及席位、面试和分配席位的权限。';
        $interviewadmin->save();
        $interviewadmin->attachPermissions(array($viewReg, $editReg, $approveReg, $editCom, $editNation, $editInterview, $assignRole));*/

        $interviewer = Role::find(1);

        $editReg = Permission::find(15);

        $interviewer->attachPermission($editReg);
    }

    /**
     * Pair roommates and partners according to name
     *
     * @return string paring result
     */
    public function autoAssign(Request $request)
    {
        $regs = Reg::where('conference_id', Reg::current()->conference_id)->whereNotIn('type', ['interviewer', 'teamadmin', 'unregistered'])->whereNotNull('reginfo')->get();
        $room = 0;
        $part = 0;
        $result1 = "";
        $result2 = "";
        $option = (object)$request->all();
        $isroommate = !empty($request->roommate);
        $ispartner = !empty($request->partner);
        foreach($regs as $reg)
        {
            if ($isroommate)
                $roommatename = $reg->getInfo('conference.roommatename') ?? '';
            $specific = $reg->specific();
            if ($isroommate && !empty($roommatename) && (in_array($specific->status, ['oVerified', 'unpaid', 'paid', 'success'])))
            {
                $roommate_id = $reg->roommate_user_id;
                if (!isset($roommate_id))
                {
                $result1 .= $reg->id ."&#09;". $reg->assignRoommateByName($option) . "<br>";
                $room++;
                }
            }
            if ($ispartner && $reg->type == 'delegate' && (in_array($specific->status, ['oVerified', 'unpaid', 'paid', 'success'])))
            {
                $partnername = $reg->getInfo('conference.partnername');
                $partner_id = $reg->specific()->partner_reg_id;
                if (!isset($partner_id))
                {
                    $result2 .= $reg->id ."&#09;". $reg->delegate->assignPartnerByName() . "<br>";
                    $part++;
                }
            }
        }
        return "えるの室友配对遍历了$room" . "行记录<br>$result1<br>えるの搭档配对遍历了$part" . "行记录<br>$result2";
    }

    public function randomAssign(Request $request)
    {
        $regs = Reg::where('conference_id', Reg::current()->conference_id)->whereIn('type', ['delegate', 'volunteer'])->where('accomodate', true)->whereNull('roommate_user_id')->get();
        $committees = Committee::where('conference_id', Reg::current()->conference_id)->where('is_dual', true)->get()->pluck(['id']);
        $delegates = Delegate::where('conference_id', Reg::current()->conference_id)->whereIn('committee_id', $committees)->whereNull('partner_reg_id')->get();
        $room = 0;
        $part = 0;
        $result1 = "";
        $result2 = "";
        $option = (object)$request->all();
        $isroommate = !empty($request->roommate);
        $ispartner = !empty($request->partner);
        if ($isroommate) {
        foreach ($regs as $reg)
        {
            $reg = $reg->fresh();
            if (!empty($reg->roommate_user_id)) continue;
            if ($reg->specific()->status != 'paid') continue;
            $result = $reg->getRandomRoommate($option);
            if ($result == 'success')
                $result = $reg->roommate->id . '&#09;' . $reg->roommate->name . '&#09; 室友已分配';
            $result1 .= $reg->id ."&#09;". $reg->user->name . '&#09;'. $result . "<br>";
            $room++;
        }
        }
        if ($ispartner) {
        foreach ($delegates as $del)
        {
            if (!empty($del->partner_user_id)) continue;
            $result = $del->getRandomPartner($option);
            if ($result == 'success')
                $result = $del->partner->reg_id . '&#09;' . $del->partner->reg->user->name . '&#09; 搭档已分配';
            $result2 .= $del->reg_id ."&#09;". $del->reg->user->name . '&#09;'. $result . "<br>";
            $part++;
        }
        }
        return "えるの室友随机分配遍历了$room" . "行记录<br>$result1<br>えるの搭档随机分配遍历了$part" . "行记录<br>$result2";
    }
    /**
     * Reset my registration to avoid ecosoc easteregg or after being rejected
     *
     * @return redirect
     */
    public function resetReg($force = false)
    {
        return view('error', ['msg' => '重置功能已被禁用，您可切换身份开启新的报名。']);
        $reg = Reg::current();
        if ($force)
            $reg->specific()->delete();
        $reg->type = 'unregistered';
        $reg->enabled = true;
        $reg->save();
        return redirect('/home');
    }
    
    /**
     * phpdoc 以后再写
     */     
    public function pairAction(Request $request)
    {
        if (!isset($request->partner) && !isset($request->roommate))
            return '请选择配对类型！';
        $result1 = "";
        $reg = Reg::current();
        if (isset($request->roommate))
            $result1 = "roommate ".$reg->assignRoommateByCode($request->code);
        if (isset($request->partner) && $reg->type == 'delegate')
        {
            if (!empty($result1)) $result1 .= "<br>";
            $result1 .= "partner ".$reg->delegate->assignPartnerByCode($request->code);
        }
        return $result1;
    }

    /**
     * phpdoc 以后再写
     */     
    public function generatePaircode(Request $request)
    {
        if (!isset($request->partner) && !isset($request->roommate))
            return '请选择配对码类型！';
        $reg = Reg::findOrFail($request->reg_id);
        $reg->generateLinkCode(isset($request->roommate), isset($request->partner));
        return 'success';
        return json_encode($request->all());
    }
    
    /**
     * phpdoc 以后再写
     */     
    public function deletePaircode($code)
    {
        DB::table('linking_codes')->where('id', $code)->delete();
        return '配对码已删除';
    }
    
    /**
     * A function to write some temporary code.
     */
    public function test(Request $request)
    {
        return Auth::user()->urlAvatar();
        return;
        $passwords = ["goldship", "vodka", "daiwascarlet", "taikishattle", "grasswonder", "hishiamazon", "mejiromcqueen", "elcondorpasa", "tmoperao", "naritabrian", "symbolirudolf", "airgroove", "agnesdigital", "seiunsky", "tamamocross", "finemotion", "biwahayahide", "mayanotopgun", "manhattancafe", "mihonobourbon", "mejiroryan", "hishiakebono", "yukinobijin", "riceshower", "inesfujin", "agnestachyon", "admirevega", "inarione", "winningticket", "airshakur", "eishinflash", "currenchan", "kawakamiprincess", "goldcity", "sakurabakushino", "seekingthepearl", "shinkowindy", "sweeptosho", "supercreek", "smartfalcon", "zennorobroy", "tosenjordan", "nakayamafesta", "naritataishin", "nishinoflower", "haruurara", "bamboomemory", "bikopegasus", "marveloussunday", "matikanefukukitaru", "mrcb", "meishodoto", "mejirodober", "nicenature", "kinghalo", "matikanetannhauser", "ikunodictus", "mejiropalmer", "daitakuhelios", "twinturbo", "satonodiamond", "kitasanblack", "sakurachiyonoo", "siriussymboli", "mejiroardan", "yaenomuteki", "tsurumarutsuyoshi", "mejirobright", "daringtact", "sakuralaurel", "naritatoproad", "yamaninzephyr", "symbolikriss", "taninogimlet", "daiichiruby", "mejiroramonu", "astonmachan", "satonocrown", "chevalgrand", "ksmiracle", "junglepocket", "copanorickey", "hokkotarumae", "wonderacute", "soundsofearth", "katsuragiace", "neouniverse", "hishimiracle", "tapdancecity", "happymeek", "bittergrasse", "littlecocon", "montjeu", "venuspark", "ligantona", "hayakawa.tazuna", "akikawa.yayoyi", "otonashi.etsuko", "kiryouin.aoi", "anshinzawa.sasami", "kashimoto.riko", "lighthello", "darleyarabian", "godolphinbarb", "byerleyturk", "satakemei"];
        foreach($passwords as $i)
        {
            echo bcrypt($i);
            echo "\r\n";
        }
        return;
        return opcache_get_status();
        $users = User::all();
        foreach ($users as $index => $value)
        {
            echo $index.'<br>';
        }
        return "404";
        //return UserController::randomAssign($request);
        $reg = Reg::find(3136);
        $reg->accomodate = false;
        $reg->save();
        $regs = Reg::where('conference_id', 3)->where('accomodate', true)->whereIn('type', ['delegate', 'volunteer'])->get();
        foreach ($regs as $reg) {
            if ($reg->specific()->status != 'paid')
                continue;
            if (empty($reg->roommate_user_id)) {
               echo $reg->user->name.",".$reg->user->identityText().",无室友,无室友<br>";
               continue;
            }
            $room_regs = $regs->where('user_id', $reg->roommate_user_id);
            /*$r = false;
            foreach ($room_regs as $room) {
                if ($room->specific()->status == 'paid') {
                    $r = true;break;}
            }
            if (!$r) {
                echo $reg->id.",".$reg->user->name.",".$reg->type."<br>";
                $reg->roommate_user_id = null;
                $reg->save();
            }*/
            echo $reg->user->name.",".$reg->user->identityText().",".$reg->roommate->name.",".$reg->roommate->identityText()."<br>";
        }
        return "done";
        $delegates = Delegate::where('conference_id', 3)->with(['partner', 'partner.reg.user', 'reg.user'])->where('status', 'paid')->get();
        foreach ($delegates as $delegate)
        {
            if ($delegate->reg->accomodate == false)
                continue;
            if (!isset($delegate->reg->roommate_user_id))
                continue;
            if (Reg::where('user_id', $delegate->reg->roommate_user_id)->where('conference_id', 3)->where('type', "delegate")->count() > 0)
                continue;
            $reg = Reg::where('user_id', $delegate->reg->roommate_user_id)->where('conference_id', 3)->first();
            if ($reg->roommate_user_id != $delegate->reg->user_id) {
                echo $delegate->reg_id.' '.$reg->user_id." mis<br>";
            }
            //echo $delegate->reg_id.' '.$delegate->reg->roommate_user_id.'<br>';
        }
        $volunteers = Volunteer::where('conference_id', 3)->with('reg.user')->where('status', 'paid')->get();
        foreach ($volunteers as $volunteer)
        {
            if ($volunteer->reg->accomodate == false)
                continue;
            if (!isset($volunteer->reg->roommate_user_id))
                continue;
            if (Reg::where('user_id', $volunteer->reg->roommate_user_id)->where('conference_id', 3)->where('type', "volunteer")->count() > 0)
                continue;
            $reg = Reg::where('user_id', $volunteer->reg->roommate_user_id)->where('conference_id', 3)->first();
            if ($reg->roommate_user_id != $volunteer->reg->user_id) {
                echo $volunteer->reg_id.' '.$reg->user_id." mis<br>";
            }
            //echo $volunteer->reg_id.' '.$volunteer->reg->roommate_user_id.'<br>';
        }
        return "done";
        echo "VOLUNTEER<br>";
        $volunteers = Volunteer::where('conference_id', 3)->with('reg.user')->where('status', 'paid')->get();
        foreach ($volunteers as $volunteer)
        {
            echo $volunteer->reg_id.','.$volunteer->reg->name().','.$volunteer->reg->getInfo('personinfo.typeDocument').','.$volunteer->reg->getInfo('personinfo.sfz').'<br>';
        }
        return "end";
        return "404";
        $delegates = Delegate::where('conference_id', 3)->with(['partner', 'partner.reg.user', 'reg.user'])->where('status', 'paid')->get();
        foreach ($delegates as $delegate)
        {
            echo $delegate->reg_id.','.$delegate->reg->name().','.$delegate->reg->getInfo('personinfo.typeDocument').','.$delegate->reg->getInfo('personinfo.sfz').'<br>';
        }
        echo "VOLUNTEER<br>";
        $volunteers = Volunteer::where('conference_id', 3)->with('reg.user')->where('status', 'paid')->get();
        foreach ($volunteers as $volunteer)
        {
            echo $volunteer->reg_id.','.$volunteer->reg->name().','.$volunteer->reg->getInfo('personinfo.typeDocument').','.$volunteer->reg->getInfo('personinfo.sfz').'<br>';
        }
        return "end";
        foreach ($delegates as $delegate)
        {
            echo $delegate->reg_id .','. $delegate->reg->user->name.',';
            $partner_id = $delegate->partner_reg_id;
            $partner = $delegate->partner;
            if (!is_object($partner))
            {
                if (!is_null($partner_id))
                    echo 'Wrong ID<br>';
                else
                    echo 'No Partner<br>';
                continue;
            }
            echo $partner->reg_id.','.$partner->reg->user->name;
            if ($partner->conference_id != 3)
                echo ',Wrong Confernece';
            if ($partner->committee_id != $delegate->committee_id)
                echo ',Committee Mismatch';
            if ($partner->status != 'paid')
            {
                $delegate->partner_reg_id = null;
                $delegate->save();
                echo ',unpaid';
            }
            if ($partner->partner_reg_id != $delegate->reg_id)
            {
                $delegate->partner_reg_id = null;
                $delegate->save();
                echo ',unpaired pair';
            }
            echo '<br>';
        }

        return '404 not found';
        if (($handle = fopen("/var/www/munpanel/app/test.csv", "r")) !== FALSE) {
            $resp = "test";
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $delegate = Delegate::find($data[0]);
                $delegate->partner_reg_id = $data[1];
                $delegate->save();
            }
            fclose($handle);
            return $resp;
        }
        $nationgroup = New Nationgroup;
        $nationgroup->name = '裁军委席位';
        $nationgroup->display_name = '共同均衡裁军谈判会议 全体席位';
        $nationgroup->save();
        $nations = Nation::where('committee_id', 12)->get();
        foreach ($nations as $nation)
        {
            $nationgroup->nations()->attach($nation);
        }
        $regs = Reg::where('conference_id', 2)->where('type', 'delegate')->with('delegate')->get();
        $ret = '';
        foreach ($regs as $reg)
        {
            $delegate = $reg->delegate;
            if ($delegate->status == 'oVerified' && (!isset($reg->order_id)))
            {
                $reg->createConfOrder();
                $ret .= $reg->id . '<br>';
            }
        }
        return $ret;
        Reg::find(3885)->createConfOrder();
        $ss = Delegate::whereIn('status', ['sVerified', 'oVerified', 'paid'])->whereNotNull('school_id')->with('reg', 'school')->get();
        foreach ($ss as $s)
        {
            $school = $s->school;
            if ($school->option('groupreg_enabled', $s->reg->conference_id))
                continue;
            $option = new Option;
            $option->conference_id = $s->reg->conference_id;
            $option->school_id = $s->school_id;
            $option->key = 'groupreg_enabled';
            $option->value = 1;
            $option->save();
        }
        return 'done';
        $regs = Reg::all();
        foreach ($regs as $reg)
        {
            if (isset($reg->school_id) && $reg->type != 'interviewer')
            {
                $specific = $reg->specific();
                if (is_object($specific))
                {
                    $specific->school_id = $reg->school_id;
                    $specific->save();
                }
            }
        }
        return 'done';
        $ret = '';
        $users = User::with('orders')->get();
        foreach($users as $user)
        {
            $orders = $user->orders->where('status', 'unpaid');
            if ($orders->count() > 0)
            {
                $sum = 0;
                foreach($orders as $order)
                {
                    $sum += $order->price;
                }
                $user->sendSMS('您尚有'.$orders->count().'笔未支付订单，共计 '.number_format($sum, 2).' 元，请尽快前往 https://portal.munpanel.com/store/orders 完成支付。推荐使用系统自动生成的二维码通过微信、支付宝或京东支付线上缴费，付款完成自动确认缴费状态，无需等待；您亦可使用会议指定的其他缴费方式并等待手动确认。感谢您的理解与支持，祝您开会愉快。');
                $ret .= $user->id.' '.$user->name. ' ' . $orders->count().' '.$sum.'<br>';
            }
        }
        return $ret;
        if (($handle = fopen("/var/www/munpanel/app/test.csv", "r")) !== FALSE) {
            $resp = "test";
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $reg = Reg::find($data[0]);
                $reg->type = 'volunteer';
                $reg->save();
                $reg->make();
                $specific = $reg->specific();
                $specific->status = 'fail';
                $specific->save();
                $resp.=$reg->name().'<br>';
                continue;
                if ($reg->conference_id == 3)
                {
                    $specific = $reg->specific();
                    if ($specific->status == 'oVerified')
                    {
                        $order = $reg->order;
                        if (is_object($order))
                        {
                            if ($order->status != 'unpaid')
                                $resp .= $data[0].' '.$order->status.'<br>';
                            else
                            {
                                $order->status = 'cancelled';
                                $order->save();
                                $reg->order_id = null;
                                $specific->status = 'sVerified';
                                $reg->save();
                                $specific->save();
                                $resp .= $data[0].' reverted (with order)<br>';
                            }
                        } else
                        {
                            $specific->status = 'sVerified';
                            $specific->save();
                            $resp .= $data[0].' reverted (without order)<br>';
                        }
                    }
                    else
                        $resp .= $data[0].' not verified '.$specific->status.'<br>';
                }
                else
                    $resp .= $data[0].' not 3<br>';
            }
            fclose($handle);
            return $resp;
        }
        $ret = '';
        $dels = Delegate::where('seat_locked', true)->with('reg')->get();
        foreach ($dels as $del)
        {
            if (!isset($del->reg->order_id))
            {
                $ret .= $del->reg_id.'<br>';
                $del->reg->createConfOrder();
            }
        }
        return $ret;
        Reg::find(2924)->createConfOrder();
        return $this->autoAssign();
        return Reg::current()->delegate->assignPartnerByCode('1245615345');
        $ots = Orgteam::where('status', 'oVerified')->where('conference_id', 3)->with('reg')->get();
        $role = Role::find(3);
        foreach($ots as $ot)
        {
            $ot->status = 'success';
            $ot->position = '会务团队成员';
            $ot->save();
            $ot->reg->attachRole($role);
        }
        $ots = Orgteam::where('status', '!=', 'oVerified')->where('status', '!=', 'success')->where('conference_id', 3)->with('reg')->get();
        foreach($ots as $ot)
        {
            $reg = $ot->reg;
            $reg->enabled = false;
            $reg->save();
        }

        Auth::login(User::find(685));
        $new = new Reg;
        $new->user_id = 685;
        $new->conference_id = 3;
        $new->school_id = null;
        $new->type = 'unregistered';
        $new->enabled = 1;
        $new->save();
        $regs = Reg::where('conference_id', 3)->whereIn('type', ['delegate', 'volunteer'])->whereNull('accomodate')->get();
        foreach ($regs as $reg)
            $reg->user->sendSMS('由于我们的疏忽，先前未向您询问您的住宿意向及室友意向，烦请您访问 https://bjmun.munpanel.com/ 根据系统提示补填信息，感谢您的理解与配合。[BJMUNSS 2017]');
        $reg1 = Reg::current();
        $reg1->enabled = false;
        $reg1->save();
        $reg2 = Reg::current();
        return $reg2->enabled;
        return Config::get('cache.ttl');

        Cache::tags('orders')->put('test', 1, 2);
        return Cache::tags('orders')->get('test');
        $satoshi = Reg::find(4166);
        return $satoshi->assignRoommateByName();

        $dais = Dais::where('status', 'fail')->get();
        foreach ($dais as $d)
        {
            $u = $d->reg;
            $u->enabled = false;
            $u->save();
        }
        return 'd';
        $teamadmins = Teamadmin::all();
        foreach($teamadmins as $teamadmin)
        {
            $reg = $teamadmin->reg;
            if (isset($reg->conference_id) && (!isset($teamadmin->conference_id)))
            {
                $teamadmin->conference_id = $reg->conference_id;
                $teamadmin->save();
            }
            if (isset($teamadmin->conference_id) && (!isset($reg->conference_id)))
            {
                $reg->conference_id = $teamadmin->conference_id;
                $reg->save();
            }
        }
        return 'gou';
        $new = new Reg;
        $new->user_id = 966;
        $new->conference_id = null;
        $new->school_id = 148;
        $new->type = 'teamadmin';
        $new->enabled = 1;
        $new->save();
        $teamadmin = new Teamadmin;
        $teamadmin->reg_id = $new->id;
        $teamadmin->school_id = 148;
        $teamadmin->save();
        $new = new Reg;
        $new->user_id = 966;
        $new->conference_id = 3;
        $new->school_id = 148;
        $new->type = 'teamadmin';
        $new->enabled = 1;
        $new->save();
        $teamadmin = new Teamadmin;
        $teamadmin->reg_id = $new->id;
        $teamadmin->school_id = 148;
        $teamadmin->save();
        return 'miao';
        $regs = Reg::all();
        foreach ($regs as $reg)
        {
            if (isset($reg->school_id) && $reg->type != 'interviewer')
            {
                $specific = $reg->specific();
                if (is_object($specific))
                {
                    $specific->school_id = $reg->school_id;
                    $specific->save();
                }
            }
        }
        return 'done';
        $new = new Reg;
        $new->user_id = 25;
        $new->conference_id = 3;
        $new->school_id = 152;
        $new->type = 'teamadmin';
        $new->enabled = 1;
        $new->save();
        $teamadmin = new Teamadmin;
        $teamadmin->reg_id = $new->id;
        $teamadmin->school_id = 152;
        $teamadmin->save();
        return 'miao';
        $mayaka = Delegate::find(3322);
        return $mayaka->assignPartnerByName();
        $ret = '';
        $users = User::with('orders')->get();
        foreach($users as $user)
        {
            if ($user->orders()->where('status', 'unpaid')->count() > 0)
            {
                $user->sendSMS('您尚有'.$user->orders()->where('status', 'unpaid')->count().'笔未支付订单，请尽快前往 https://portal.munpanel.com/store/orders 完成支付，感谢。');
                $ret .= $user->id.' '.$user->name. ' ' . $user->orders()->where('status', 'unpaid')->count().'<br>';
            }
        }
        return $ret;
        Cache::tags('orders')->put('test', 1, 2);
        return Cache::tags('orders')->get('test');
        $dels = Delegate::where('seat_locked', true)->with('reg')->get();
        foreach ($dels as $del)
        {
            if (!isset($del->reg->order_id))
                $del->reg->createConfOrder();
        }
        return 'test';
        $reg = Reg::find(3622);
        $reg->createConfOrder();
        return 'meow';
        return url()->current();
        return 'test';
        return Reg::current()->createConfOrder();
        $regs = Reg::where('conference_id', 3)->whereIn('type', ['delegate', 'volunteer'])->get();
        $i = 0;
        foreach ($regs as $reg)
        {
            $reginfo = json_decode($reg->reginfo);
            if (!empty($reginfo->conference->roommatename))
                $reg->accomodate = true;
            $reg->save();
            $i++;
        }
        return "已更新 $i 人的住宿信息";
        return '...';
        $reg = new Reg;
        $reg->user_id = 1080;
        $reg->type = 'teamadmin';
        $reg->enabled = 1;
        $reg->school_id = 96;
        $reg->save();
        $reg = new Reg;
        $reg->user_id = 1080;
        $reg->type = 'teamadmin';
        $reg->enabled = 1;
        $reg->school_id = 96;
        $reg->save();
        $teamadmin = new Teamadmin;
        $teamadmin->reg_id = $reg->id;
        $teamadmin->school_id = 96;
        $teamadmin->save();
        return 'done';
        return session('_previous.url');
        $com = Committee::find(24);
        $setas = $com->nations;
        foreach($setas as $t)
        {
            $t->nationgroups()->attach(28);
        }
        return 'gua';
        $i = 0;
        $teams = School::where('id', '<', 94)->get();
        foreach ($teams as $team)
        {
            $team->joinCode = generateID(32);
            $team->save();
            $i++;
        }
        return "已为 $i 个既有团队分配加入码";
        return (is_object(Reg::current()->specific())) ? 'true' : 'false';
            $date = date_sub(date_create('2017-05-31 10:30:00'), new \DateInterval('P3D'));
            $deelegates = Delegate::whereNotNull('nation_id')->where('seat_locked', false)->where('updated_at', '<', date('Y-m-d H:i:s', $date->getTimestamp()))->get()->pluck('reg_id');
            $nations = Delegate::whereNotNull('nation_id')->where('seat_locked', false)->where('updated_at', '<', date('Y-m-d H:i:s', $date->getTimestamp()))->get()->pluck('nation_id');
            Delegate::whereIn('reg_id', $deelegates)->update(['seat_locked' => true]);
            // TODO: ADD EVENT
            Nation::whereIn('id', $nations)->update(['status' => 'locked']);
            $regs = Reg::whereIn('id', $deelegates)->get();
            foreach($regs as $reg) 
            {
                $delegate = $reg->delegate;
                if ($delegate->committee_id != $delegate->nation->committee_id)
                    $reg->addEvent('committee_moved', '{"name":" MUNPANEL 自动","committee":"'.$delegate->nation->committee->display_name.'"}');
                $delegate->committee_id = $delegate->nation->committee_id;
                $delegate->save();
                $reg->addEvent('role_locked', '{"name":" MUNPANEL 自动"}');
            }
            return json_encode(Nation::whereIn('id', $nations)->get());
            return $deelegates->count();
        $delegates = Reg::where('conference_id', 2)->where('enabled', false)->get();
        foreach ($delegates as $delegate)
        {
            $delegate->enabled = true;
            $delegate->save();
        }
        return 'meow';
        if (($handle = fopen("/var/www/munpanel/test.csv", "r")) !== FALSE) {
            $resp = "test";
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $reg = Reg::find($data[0]);
                if ($reg->conference_id == 2)
                {
                        $reg->enabled = false;
                        $reg->save();
                }
                else
                    $resp .= $data[0].'<br>';
            }
            fclose($handle);
            return $resp;
        }
    $results = [ 11 => "北京", "天津", "河北", "山西", "内蒙古",
        21 => "辽宁", "吉林", "黑龙江",
        31 => "上海", "江苏", "浙江", "安徽", "福建", "江西", "山东",
        41 => "河南", "湖北", "湖南", "广东", "广西", "海南",
        50 => "重庆", "四川", "贵州", "云南", "西藏",
        61 => "陕西", "甘肃", "青海", "宁夏", "新疆",
        71 => "台湾",
        81 => "香港", "澳门",
        99 => "海外" ];
        $res = array();
        foreach ($results as $key => $value)
        {
            $test = array();
            $test['value'] = $key;
            $test['text'] = $value;
            $res[] = $test;
        }
        return json_encode($res);
        $regs = Reg::where('conference_id', 3)->where('type', 'volunteer')->get();
        foreach ($regs as $reg)
                $reg->addEvent('registration_submitted', '');
        dd($regs);
        $users = User::all();
        foreach ($users as $user)
        {
            if ($user->telVerifications != -1)
            {
                $user->telVerifications += 95;
                $user->save();
            }
        }
        return 'Meow!';
        $nations = Nation::with('committee')->get();
        foreach($nations as $nation)
        {
            $nation->conference_id = $nation->committee->conference_id;
            $nation->save();
        }
        return 'miao';
        dd(Delegate::find(22)->canAssignSeats());
        return 'gou';
        $dels = Delegate::where('conference_id', 2)->with('reg')->get();
        $ret = '';
        foreach($dels as $del)
        {
            $info = json_decode($del->reg->reginfo);
            $ret .= $del->reg_id;
            $ret .= ',';
            $ret .= $del->reg->name();
            $ret .= ',';
            $ret .= $info->personinfo->school;
            $ret .= ','.$del->reg->user->tel;
            $com = null;
            $ret .= ',';
            if (isset($info->conference->branch1)) {
                $com =Committee::find($info->conference->branch1);
                if (is_object($com))
                    $ret .= $com->name;
            }
            $com = null;
            $ret .= ',';
            if (isset($info->conference->branch2)) {
                $com =Committee::find($info->conference->branch2);
                if (is_object($com))
                    $ret .= $com->name;
            }
            $com = null;
            $ret .= ',';
            if (isset($info->conference->branch3)) {
                $com =Committee::find($info->conference->branch3);
                if (is_object($com))
                    $ret .= $com->name;
            }
            $com = null;
            $ret .= ',';
            if (isset($info->conference->branch4)) {
                $com =Committee::find($info->conference->branch4);
                if (is_object($com))
                    $ret .= $com->name;
            }
            $ret .= ','. $del->statusText();
            $ret .= "<br>";
        }
        return $ret;
        dd(extract_mention($request->text));
        dd(geoip(\Request::ip()));
        $ints = Interviewer::all();
        foreach($ints as $int)
        {
            $int->reg->type = 'interviewer';
            $int->reg->save();
            $int->reg->roles()->detach();
        }
        return ".";
        return secure_url('https://test.com//test');
        $this->createPermissions();
        return '<a href="http://192.154.111.163/phpmyadmin">检查数据库</a>';
        $js = json_encode('$("#reg2Form").ready(function(e){
    var group = document.getElementById("committee1branch");
    var group2 = document.getElementById("committee2branch");
    group2.style.display = "none";
    group.style.display = "none";
});
$("#select-comm1").change(function(e){
    var group = document.getElementById("committee1branch");
    var group2 = document.getElementById("committee2branch");
    if (document.getElementById("select-comm1").value == "11")
    {
        $("select#select-comm2 option").remove();
        $("select#select-comm2").append(\'<option value="" selected="">请选择</option><option value="10">危机联动体系</option><option value="29">独立委员会组</option>\');
        $("select#select-branch1 option").remove();
        $("select#select-branch1").append(\'<option value="11" selected="">东晋纵横</option>\');
        group.style.display = "none";
    }
    else if (document.getElementById("select-comm1").value == "10")
    {
        $("select#select-branch1 option").remove();
        $("select#select-branch2 option").remove();
        $("select#select-branch1").append(\'<option value="" selected="">请选择</option><option value="17">亚洲分支</option><option value="18">欧洲分支</option><option value="19">东欧分支</option><option value="20">中东分支</option><option value="21">联合国安全理事会</option><option value="22">美洲分支</option><option value="24">舆论媒体</option>\');
        $("select#select-branch2").append(\'<option value="" selected="">请选择备选会场</option><option value="17">亚洲分支</option><option value="18">欧洲分支</option><option value="19">东欧分支</option><option value="20">中东分支</option><option value="21">联合国安全理事会</option><option value="22">美洲分支</option><option value="24">舆论媒体</option>\');
        $("select#select-comm2 option").remove();
        $("select#select-comm2").append(\'<option value="" selected="">请选择</option><option value="11">東晉縱橫</option><option value="29">独立委员会组</option>\');
        group2.style.display = "none";
        group.style.display = "block";
    }
    else
    {
        $("select#select-branch1 option").remove();
        $("select#select-branch2 option").remove();
        $("select#select-branch1").append(\'<option value="" selected="">请选择</option><option value="12">共同均衡裁军谈判会议</option><option value="13">G20 Summit</option><option value="14">联合国世界旅游组织第二十二届全体大会</option><option value="15">慕尼黑安全政策会议</option><option value="16">联合国大会社会、人道主义和文化委员会</option>\');
        $("select#select-branch2").append(\'<option value="" selected="">请选择备选会场</option><option value="12">共同均衡裁军谈判会议</option><option value="13">G20 Summit</option><option value="14">联合国世界旅游组织第二十二届全体大会</option><option value="15">慕尼黑安全政策会议</option><option value="16">联合国大会社会、人道主义和文化委员会</option>\');
        $("select#select-comm2 option").remove();
        $("select#select-comm2").append(\'<option value="" selected="">请选择</option><option value="10">危机联动体系</option><option value="11">東晉縱橫</option><option value="29">独立委员会组</option>\');
        group.style.display = "block";
    }
});
$("#select-comm2").change(function(e){
    var group = document.getElementById("committee2branch");
    var e1 = document.getElementById("select-branch3");
    var e2 = document.getElementById("select-branch4");
    if (document.getElementById("select-comm2").value == "11")
    {
        $("select#select-branch3 option").remove();
        $("select#select-branch3").append(\'<option value="11" selected="">东晋纵横</option>\');
        group.style.display = "none";
    }
    else if (document.getElementById("select-comm2").value == "10")
    {
        $("select#select-branch3 option").remove();
        $("select#select-branch4 option").remove();
        $("select#select-branch3").append(\'<option value="" selected="">请选择</option><option value="17">亚洲分支</option><option value="18">欧洲分支</option><option value="19">东欧分支</option><option value="20">中东分支</option><option value="21">联合国安全理事会</option><option value="22">美洲分支</option><option value="24">舆论媒体</option>\');
        $("select#select-branch4").append(\'<option value="" selected="">请选择备选会场</option><option value="17">亚洲分支</option><option value="18">欧洲分支</option><option value="19">东欧分支</option><option value="20">中东分支</option><option value="21">联合国安全理事会</option><option value="22">美洲分支</option><option value="24">舆论媒体</option>\');
        group.style.display = "block";
    }
    else
    {
        $("select#select-branch3 option").remove();
        $("select#select-branch4 option").remove();
        $("select#select-branch3").append(\'<option value="" selected="">请选择</option><option value="12">共同均衡裁军谈判会议</option><option value="13">G20 Summit</option><option value="14">联合国世界旅游组织第二十二届全体大会</option><option value="15">慕尼黑安全政策会议</option><option value="16">联合国大会社会、人道主义和文化委员会</option>\');
        $("select#select-branch4").append(\'<option value="" selected="">请选择备选会场</option><option value="12">共同均衡裁军谈判会议</option><option value="13">G20 Summit</option><option value="14">联合国世界旅游组织第二十二届全体大会</option><option value="15">慕尼黑安全政策会议</option><option value="16">联合国大会社会、人道主义和文化委员会</option>\');
        group.style.display = "block";
    }
});');
return view('blank',['testContent' => $js, 'convert' => false]);
        $a = Reg::current()->delegate->assignments()->where('reg_assignment', 1);
        $arr = [];
        foreach ($a as $item)
            array_push($arr, $item->id);
        $b = Assignment::whereIn('id', $arr)->whereDoesntHave('handins', function ($query) {
            $query->where('reg_id', Reg::currentID());
        })->count();
        return $b;
        $c = $b->whereDoesntHave('handins', function ($query) {
            $query->where('reg_id', Reg::currentID());
        });
        // dd(\App\Interviewer::list());
        if (($handle = fopen("/var/www/munpanel/test.csv", "r")) !== FALSE) {
            $resp = "";
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $user = User::where('name', $data[0])->get();
                $resp .= json_encode($user) . '<br>';
            }
            fclose($handle);
            return $resp;
        }

        $orders = Order::all();
        foreach ($orders as $order)
        {
            if ($order->status != 'paid')
                continue;
                $c = json_decode($order->content);
             foreach ($c as $row)
        {
            $id = $row->id;
            if (substr($id, 0, 4) == 'NID_')
                continue;
            $good = Good::find($id);
            if (is_object($good))
            {
                //if ($good->remains > 0) {
                    $good->remains -= $row->qty;
                    $good->save();
                //} else {
                 //   return view('error', ['msg' => '您的购物车中有商品已售空']);
                //}
            } else {
                //return view('error', ['msg' => '您的购物车中有商品已下架']);
            }
        }

        }
        return '...';
        $good = new Good;
        $good->name = 'BJMUN 徽章（小）';
        $good->image = 'storeitem/badge1.jpeg';
        $good->price = 5;
        $good->remains = 50;
        $good->save();
        $good = new Good;
        $good->name = 'BJMUN 徽章（大）';
        $good->image = 'storeitem/badge2.jpeg';
        $good->price = 7;
        $good->remains = 40;
        $good->save();
        $good = new Good;
        $good->name = 'BJMUN 书签';
        $good->image = 'storeitem/bookmark.jpeg';
        $good->price = 49;
        $good->remains = 10;
        $good->save();
        $good = new Good;
        $good->name = 'BJMUN 马克杯（陶瓷）';
        $good->image = 'storeitem/cup1.jpeg';
        $good->price = 25;
        $good->remains = 20;
        $good->save();
        $good = new Good;
        $good->name = 'BJMUN 马克杯（变色）';
        $good->image = 'storeitem/cup2.jpeg';
        $good->price = 45;
        $good->remains = 18;
        $good->save();
        $good = new Good;
        $good->name = 'BJMUN 帽衫（XXL）';
        $good->image = 'storeitem/hoot.jpeg';
        $good->price = 129;
        $good->remains = 8;
        $good->save();
        $good = new Good;
        $good->name = 'BJMUN 帽衫（L）';
        $good->image = 'storeitem/hoot.jpeg';
        $good->price = 129;
        $good->remains = 8;
        $good->save();
        $good = new Good;
        $good->name = 'BJMUN 钥匙链';
        $good->image = 'storeitem/key.jpeg';
        $good->price = 15;
        $good->remains = 50;
        $good->save();
        $good = new Good;
        $good->name = 'BJMUN U盘（8GB, USB2.0）';
        $good->image = 'storeitem/drive.jpeg';
        $good->price = 49;
        $good->remains = 45;
        $good->save();
        $good = new Good;
        $good->name = 'BJMUN 手机壳（iPhone 6/6S Plus）';
        $good->image = 'storeitem/phone.jpeg';
        $good->price = 29;
        $good->remains = 20;
        $good->save();
        return "gouli";
        $users = User::all();
        foreach($users as $user)
        {
            if ($user->type == 'delegate' && Delegate::find($user->id) == null)
            {
                $user->type = 'unregistered';
                $user->save();
            }
            if ($user->type == 'volunteer' && Delegate::find($user->id) == null)
            {
                $user->type = 'unregistered';
                $user->save();
            }
        }
        $assign = $this->autoAssign();
        return $assign;
        //$delgroup = new Delegategroup;
        //$delgroup->name = 'UNSC媒体';
        //$delgroup->display_name = 'UNSC媒体代表';
        //$delgroup->save();
        //$delgroup = Delegategroup::find(5);
        $delegates = Committee::find(2)->delegates;
        foreach ($delegates as $delegate)
        {
                $delegate->committee_id = 1;//$delgroup->delegates()->attach($delegate);
                $delegate->save();
        }
        /*$delgroup = new Delegategroup;
        $delgroup->name = 'UNSC国家';
        $delgroup->display_name = 'UNSC国家代表';
        $delgroup->save();
        $delegates = Committee::find(1)->delegates;
        foreach ($delegates as $delegate)
                $delgroup->delegates()->attach($delegate);*/
        return 'Aloha';
        /*$schools = School::all();
        foreach($schools as $school)
        {
            if ($school->user_id != 1)
            {
                $school->payment_method = 'group';
                $school->save();
            }
        }
        return 'ha';
        $delegates = Delegate::all();
        $i = 0;
        $result = "";
        foreach($delegates as $delegate)
        {
            if (isset($delegate->partnername))
            {
                $result .= "ID\t".$delegate->user->id ."\t". $delegate->assignPartnerByName() . "\n***";
                $i++;
            }
        }
        return "えるの搭档配对遍历了$i" . "行记录\n$result";*/
        $assign = $this->autoAssign();
        return $assign;
        $assignment = new Assignment;
        $assignment->subject_type = 'nation';
        $assignment->handin_type = 'upload';
        $assignment->title = 'ECOSOC 背景指导学术作业';
        $assignment->description = '<h4>题目</h4>1.请从工业化和基础设施建设中任选一角度，分析其对于城市发展的作用。<br>2.都市农业发展于20世纪上半叶，最初起源于日本与欧美等发达国家。日本的都市农业面积小且分散，但凭借其生产资料运输方便和经营者掌握高技术等优势在城市中具有强大的生命力。都市农业在城市中具有极为广泛的作用，例如保障城市居民的食品供应，改善周围的生态环境等。（下面a、b两问均需作答）<br>a.请简要叙述日本都市农业的形成原因（不多于500字）<br>b.请从可持续发展的角度分析日本都市农业的功能以及作用。（字数不限，请分条阐述）<br><br><h4>要求</h4>请每一对搭档共同完成一份学术作业，将两道题的作答写在一个.doc或.docx格式的文件中，于北京时间2017年1月21日晚23：59分前上传MUNPANEL系统。<br><br>本次会议学术作业均请各位代表独立完成，学术作业的全部内容需是撰写学术作业者自行完成的结果，请勿使撰写学术作业者之外的任何人对于学术作业参与包括但不限于：撰写、部分撰写、修改、点评等影响学术作业的行为，一经发现将被视为学术不端进行处理。<br><nt>在学术作业撰写时，鼓励各位代表进行各类资料的查阅。但主席团禁止任何形式的抄袭，若在学术作业的撰写过程中需要对于资料进行参考或引用，请在文中以脚注形式标注出引用文段，并在文后列举撰写过程中全部的参考资料。若对于学术资料进行引用但未标注，也会同样被认定为抄袭。<br><br>在本次会议中，被发现有任何学术不端行为的代表将被立即取消全部的评奖资格。<br><br>参考与引用标注方式如下：<br>书籍类：作者：《文献名》，出版社，出版年，页码。<br>论文与报刊类：作者：《文献名》，《刊物名》和期数。<br>外文类：作者，文献名（斜体），出版地：出版社或报刊名，时间，页码。<br>网络内容：文章主题，网络链接<br>其他形式的参考引用内容请自行注明';
        $assignment->deadline = '2017-01-21 23:59:59';
        $assignment->save();
        $committee = Committee::find(9);
        $committee->assignments()->attach($assignment);
        return 'aloha';
        $delegategroup = Delegategroup::find(4);
        $delegategroup->assignments()->attach($assignment);
        return '...';
        $delegategroup = new Delegategroup;
        $delegategroup->name = '非成员校ECOSOC';
        $delegategroup->display_name = '非成员校ECOSOC代表';
        $delegategroup->save();
        $delegategroup = Delegategroup::find(1);
        $delegates = Committee::find(1)->delegates;
        $delegates->load('school');
        foreach ($delegates as $delegate)
                if ($delegate->school->user_id != 1 && $delegategroup->delegates()->find($delegate->user_id) === null)
                        $delegategroup->delegates()->attach($delegate);
        /*$delegates = Committee::find(2)->delegates;
        $delegates->load('school');
        foreach ($delegates as $delegate)
                if ($delegate->school->user_id == 1)
                        $delegategroup->delegates()->attach($delegate);*/
        return 'hello';
        return Reg::current()->delegate->nation->name;
        return Assignment::find(1)->belongsToDelegate(9);
        return response()->json(Reg::current()->delegate->assignments());
        return Auth::user()->invoiceAmount();
        return Auth::user()->invoiceItems();
        return "gou";
    }

    /**
     * Verify an email using a token.
     *
     * @param string $email the email to be verified
     * @param string $token the token to verify
     * @return string|Illuminate\Http\Response
     */
    public function doVerifyEmail($email, $token)
    {
        $user = User::where('email', $email)->firstOrFail();
        if ($user->emailVerificationToken == 'success' || $user->emailVerificationToken == $token)
        {
            $user->emailVerificationToken = 'success';
            $user->save();
            return redirect('/home');
        }
        return 'Token mismatch!';
    }

    /**
     * Send a verification code to a mobile using sms/call.
     * Then, display a modal for the user to input the code.
     *
     * @param Request $request
     * @param string $method 'sms' or 'call'
     * @param string $tel the number to be called/messaged
     * @return string|Illuminate\Http\Response
     */
    public function verifyTelModal(Request $request, $method, $tel)
    {
        if (!isset($tel))
            return view('errorModal', ['msg' => '您输入的手机号码为空，或您的浏览器暂不兼容 intl-tel-input 组件。推荐使用 Google Chrome。']);
        $user = Auth::user();
        $oldTime = $request->session()->get('codeTime');
        $nowTime = time();
        if ((!isset($oldTime) || $nowTime > $oldTime + 58) && $user->telVerifications > 0)
            $request->session()->put('codeTime', $nowTime);
        else
            return view('errorModal', ['msg' => '致歉您的尝试太过频繁，请明天重试。']);
        $user->tel = $tel;
        $user->save();
        $code = mt_rand(1000, 9999);
        $request->session()->flash('code', $code);
        if ($method == 'sms')
        {
            if (!$user->sendSMS('感谢您使用 MUNPANEL 系统。您的验证码为'.$code.'。'))
                return view('errorModal', ['msg' => '发送短信出错！请检查您的电话号码是否正确。']);
            //SmsController::send([$tel], '尊敬的'.$user->name.'，感谢您使用 MUNPANEL 系统。您的验证码为'.$code.'。');
        }
        else if ($method == 'call')
        {
            if(!SmsController::call($tel, $code))
                return view('errorModal', ['msg' => '拨打电话出错！抱歉我们暂不支持较多国家的电话服务，请检查您的电话号码是否正确，并尝试使用短信激活您的账户']);
        }
        else
            return view('errorModal', ['msg' => '错误的验证方式']);
        $user->telVerifications--;
        return view('verifyTelModal');
    }

    /**
     * Verify a phone number.
     *
     * @param Request $request
     * @return string|Illuminate\Http\Response
     */
    public function doVerifyTel(Request $request)
    {
        $correct = $request->session()->get('code');
        $code = $request->code;
        if (isset($correct) && $correct == $code)
        {
            $user = Auth::user();
            $user->telVerifications = -1;
            $user->save();
            return redirect()->intended('/home');
        } else {
            return redirect('/verifyTel'); //To-Do: error prompt
        }
    }

    /**
     * Resend verification mail to the logged in user.
     *
     * @return Illuminate\Http\Response
     */
    public function resendRegMail()
    {
        $user = Auth::user();
        $user->sendVerificationEmail();
        return redirect('/verifyEmail');
    }

    /**
     *
     */
    public function doSwitchIdentity(Request $request)
    {
        if ($request->reg == 'logout') {
            Auth::logout();
            return redirect('/login');
        }
        if ($request->reg == 'new') {
            $reg = Auth::user()->regs()->where('enabled', true)->where('conference_id', Reg::currentConferenceID())->where('type', 'unregistered')->first();
            if (!is_object($reg))
                $reg = Reg::create(['conference_id' => Reg::currentConferenceID(), 'user_id' => Auth::id(), 'type' => 'unregistered', 'enabled' => true]);
            $reg->login(true);
            return redirect('/home');
        }
        $reg = Reg::findOrFail($request->reg);
        $target = '/home';
        if ($reg->user_id != Auth::id())
        {
            if (is_object(Reg::current()))
            {
                if (Reg::current()->conference_id == $reg->conference_id && Reg::current()->can('sudo'))
                {
                    $reg->sudo();
                    $target = '/aboutSudo';
                }
                else
                    return 'error';
            } else
                return 'error';
        } else
            $reg->login(true);
        return redirect(isset($request->target) ? $request->target : $target);
    }

    public function atwhoList()
    {
        $list = new Collection;
        $ids = DB::table('users')
            ->whereRaw('exists (select 1 from `regs` where regs.user_id = users.id and `conference_id` = \''.Reg::currentConferenceID().'\' and `enabled` = \'1\' and (`type` = \'interviewer\' or (`type` = \'ot\' and exists (select 1 from `ot_info` where ot_info.reg_id = regs.id AND ot_info.status = \'success\')) or (`type` = \'dais\' and exists (select 1 from `dais_info` where dais_info.reg_id = regs.id AND dais_info.status = \'success\'))))')
            /*
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('regs')
                    ->whereRaw('regs.user_id = users.id')
                    ->where('conference_id', Reg::currentConferenceID())
                    ->where('enabled', true)
                    ->where(function ($query) {
                        $query->where('type', 'interviewer')
                            ->orWhere(function ($query) {
                                $query->where('type', 'ot')
                                    ->whereExists(function ($query) {
                                        $query->select(DB::raw(1))
                                            ->from('ot_info')
                                            ->whereRaw('ot_info.reg_id = regs.id AND ot_info.status = \'success\'');
                                    });
                            })->orWhere(function ($query) {
                                $query->where('type', 'dais')
                                    ->whereExists(function ($query) {
                                        $query->select(DB::raw(1))
                                            ->from('dais_info')
                                            ->whereRaw('dais_info.reg_id = regs.id AND dais_info.status = \'success\'');
                                    });
                            });
                    });
            })*/
            ->orderBy('id')
            ->pluck('id');

        $users = User::whereIn('id', $ids)->with('regs', 'regs.delegate', 'regs.delegate.committee', 'regs.volunteer', 'regs.observer', 'regs.dais', 'regs.dais.committee', 'regs.ot', 'regs.interviewer', 'regs.interviewer.committee')->get(['id', 'name']);
        $result = array();
        foreach($users as $user)
        {
            $result[] = array(
                    'id' => $user->id,
                    'name' => $user->name,
                    'position' => $user->identityText(),
            );
        }
        return json_encode($result);
        dd($ids);
        $ots = Orgteam::where('conference_id', Reg::currentConferenceID())->where('status', 'success')->with('reg', 'reg.user')->get();
        foreach ($ots as $ot)
            if (!$list->contains($ot->reg->user) && $ot->status == 'success')
                $list->push($ot->reg->user);
        $daises = Dais::where('conference_id', Reg::currentConferenceID())->where('status', 'success')->with('reg', 'reg.user')->get();
        foreach ($daises as $dais)
            if (!$list->contains($dais->reg->user) && $dais->status == 'success')
                $list->push($dais->reg->user);
        $interviewers = Interviewer::where('conference_id', Reg::currentConferenceID())->with('reg', 'reg.user')->get();
        foreach ($interviewers as $interviewer)
            if (!$list->contains($interviewer->reg->user))
                $list->push($interviewer->reg->user);
        $sorted = $list->sortBy('id');
        $sorted->load('regs', 'regs.delegate', 'regs.delegate.committee', 'regs.volunteer', 'regs.observer', 'regs.dais', 'regs.dais.committee', 'regs.ot');
        $result = array();
        foreach($sorted as $user)
        {
            $result[] = array(
                    'id' => $user->id,
                    'name' => $user->name,
                    'position' => $user->identityText(),
            );
        }
        return json_encode($result);
    }

    public function updateReg(Request $request, $id)
    {
        $reg = Reg::findOrFail($id);
        if (Reg::current()->type == 'ot' || (Reg::current()->type == 'teamadmin' && Reg::current()->school_id == $reg->school_id && in_array($reg->specific()->status, ['reg', 'sVerified'])) || (Reg::currentID() == $reg->id && $reg->specific()->status == 'reg'))
        {
            $name = $request->get('name');
            $value = $request->get('value');
            if ($reg->conference_id != Reg::currentConferenceID())
                return 'error';
            $reg->updateInfo($name, $value);
            $reg->save();
        } else
            return 'error';
    }

    public function disabledHome()
    {
        if (Reg::current()->enabled)
            return redirect('home');
        return view('disabledHome');
    }

    public function doSelectTeam(Request $request)
    {
        $conf = Reg::currentConference();
        if ($conf->option('group_disabled'))
            return view('error', ['msg' => 'Team disabled in this conference.']);

        $reg = Reg::current();
        if (is_object($reg->school))
            return view('error', ['msg' => 'Already selected a team!']);

        $team = School::find($request->team);
        if (!is_object($team))
            return view('error', ['msg' => 'Team does not exist!']);

        if (DB::table('school_user')
            ->whereUserId(Reg::current()->user_id)
            ->whereSchoolId($team->id)
            ->count() == 0)
            return view('error', ['msg' => 'Not a member, join first!']);

        if ($conf->option('groupreg_enabled', $team->id)) {
            $reg->school_id = $team->id;
            $reg->save();
            $specific = $reg->specific();
            if (is_object($specific)) {
                $specific->school_id = $team->id;
                if ($specific->status == 'sVerified')
                    $specific->status = 'reg';
                $specific->save();
            }
            return back();
        } else
            return view('error', ['msg' => 'Team has not allowed group registration in this conference yet. Please ask your team admins to enable it.']);
    }

    public function createTeamAdmin()
    {
        $reg = Reg::current();
        $team = $reg->school;
        if (!is_object($team))
            return 'error';
        if (!$team->isAdmin())
            return 'Permission Denied!';
        $conf = Reg::currentConference();
        if ($conf->option('group_disabled'))
            return 'Team disabled in this conference.';
        $newreg = new Reg;
        $newreg->user_id = $reg->user_id;
        $newreg->conference_id = $conf->id;
        $newreg->type = 'teamadmin';
        $newreg->enabled = 1;
        $newreg->school_id = $team->id;
        $newreg->save();
        $teamadmin = new Teamadmin;
        $teamadmin->reg_id = $newreg->id;
        $teamadmin->school_id = $team->id;
        $teamadmin->conference_id = $conf->id;
        $teamadmin->save();
        return redirect(mp_url('/doSwitchIdentity/'.$newreg->id));
    }
}
