<?php
/**
 * Copyright (C) MUNPANEL
 * This file is part of MUNPANEL System.
 *
 * Open-sourced under AGPL v3 License.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Yajra\Datatables\Datatables;
use App\School;
use App\Conference;
use App\User;
use App\Option;

class SchoolController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function schools()
    {
        return view('school.list');
    }
    
    public function schoolPortal()
    {
        return view('schoolPortal');
    }

    /**
     * Show schools datatables json
     *
     * @return string JSON of schools
     */
    public function teamsTable()
    {
        $result = new Collection;
        $schools = School::all();
        foreach ($schools as $school)
        {
            $result->push([
                'details' => '',//'<a href="schools/'. $school->id .'/details.modal" data-toggle="ajaxModal" id="'. $school->id .'" class="details-modal"><i class="fa fa-search-plus"></i></a>',
                'id' => $school->id,
                'type' => $school->typeText(),
                'name' => $school->name,
                'admin' => ''//$school->schooladmins_count > 0 ? '是':'否'
            ]);
        }
        return Datatables::of($result)->make(true);
    }

    public function newTeamModal()
    {
        if (!Auth::check())
            return redirect('/login');
        return view('portal.newTeam');
    }

    public function joinTeamModal($id = 0)
    {
        if (!Auth::check())
            return redirect('/login');
        if ($id != 0)
        {
            $school = School::findOrFail($id);
            return view('portal.joinTeam', ['school' => $school]);
        }
        return view('portal.joinTeam');
    }

    public function detailsModal($id)
    {
        $school = School::findOrFail($id);
        if (DB::table('school_user')
            ->whereUserId(Auth::id())
            ->whereSchoolId($id)
            ->count() > 0)
            return view('portal.schoolDetailsModal', ['school' => $school, 'isAdmin' => $school->isAdmin()]);
        return 'error';
    }

    public function schoolIndex($id)
    {
        $school = School::findOrFail($id);
        if (is_object($school))
            return view('school.index', ['school' => $school]);
        return view('noExist', ['item' => "学校"]);
    }

    public function createTeam(Request $request)
    {
        $uid = Auth::id();
        $count = School::where('name', $request->name)->count();
        if ($count > 0)
            return "school already exists";
        if ($count == 0)
        $school = new School;
        $school->name = $request->name;
        $school->type = $request->type;
        $school->description = $request->description;
        $school->joinCode = generateID(32);
        $school->save();
        $school->users()->attach([$uid => [
            'status' => 'master',
            'title' => $request->title,
            'grade' => $request->grade,
            'gradeYear' => $request->gradeyear,
            'class' => $request->class
        ]]);
        return view('school.index', ['school' => $school]);
    }

    public function joinTeam(Request $request)
    {
        $code = $request->code;
        $school = null;
        $status = 'pending';
        $title = $request->title;
        if ($code != '')
        {
            $school = School::where('joinCode', $code)->first();
            if (!is_object($school))
                return view('error', ['msg' => 'Wrong Code... Please double-check it.']);
            else 
            {
                $status = 'active';
                $title = '社团成员';
            }
        }
        elseif (isset($request->schoolid))
            $school = School::find($request->schoolid);
        else
            $school = School::where('name', $request->schoolname)->first();
        if (!is_object($school))
            return view('error', ['msg' => 'The school you are willing to join does not exist!']);
        if (DB::table('school_user')
            ->whereUserId(Auth::id())
            ->whereSchoolId($school->id)
            ->count() > 0)
            return view('error', ['msg' => 'Already Member!']);
        $school->users()->attach([Auth::id() => [
            'status' => $status,
            'title' => $title,
            'grade' => $request->grade,
            'gradeYear' => $request->gradeyear,
            'class' => $request->class
        ]]);
        return view('school.index', ['school' => $school]);
    }

    /**
     * Update a property of a team.
     *
     * @param Request $request
     * @param int $id the id of the team to be updated
     * @return void
     */
    public function updateTeam(Request $request, $id)
    {
        $school = School::findOrFail($id);
        if (!$school->isAdmin())
            return 'error';
        $name = $request->get('name');
        $value = $request->get('value');
        $school->$name = $value;
        $school->save();
    }

    public function teamAdmin($id)
    {
        $school = School::findOrFail($id);
        if (!$school->isAdmin())
            return 'error';
        return view('portal.teamAdmin', ['team' => $school]);
    }

    public function teamMembers($id)
    {
        $school = School::findOrFail($id);
        if (!$school->isAdmin())
            return 'error';
        return view('portal.teamMembers', ['team' => $school]);
    }

    /**
     * Show team member datatables json
     *
     * @return string JSON of team members
     */
    public function groupMemberTable($id)
    {
        $user = Auth::user();
        $school = School::findOrFail($id);
        if ($school->isAdmin())
        {
            $result = new Collection;
            $users = User::with(['regs.teamadmin' => function($query) use($school) {
                $query->whereRaw('teamadmins.school_id = ' . $school->id);
            }])->whereExists( function($query) use($school, $user) {
                $query->select(DB::raw(1))
                      ->from('school_user')
                      ->whereRaw('school_user.user_id = users.id and school_user.school_id=' . $school->id);
            })->get(['id', 'email', 'name', 'tel']);
            foreach ($users as $user)
            {
                $globalAdmin = false;
                $confAdmins = 0;
                // TODO: ignore conference with status in ['finished', 'cancelled']
                foreach ($user->regs as $reg)
                {
                    if (is_object($reg->teamadmin))
                    {
                        if ($reg->conference_id == 0)
                        {
                           $globalAdmin = true;
                           break;
                        } else
                            $confAdmins++;
                    }
                }
                $adminText = '否';
                if ($globalAdmin)
                    $adminText = '全局';
                elseif ($confAdmins > 0)
                    $adminText = $confAdmins.'场会议';
                $hasAdmin = ($adminText != '否') ? true : false;
                $adminText .= '&nbsp;<a href="'.mp_url('schools/'.$id.'/groupMember/'.$user->id.'/addAdmin.modal').'" data-toggle="ajaxModal"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>';
                if ($hasAdmin)
                    $adminText .= '&nbsp;<a href="'.mp_url('schools/'.$id.'/groupMember/'.$user->id.'/delAdmin.modal').'" data-toggle="ajaxModal"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>';
                $result->push([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tel' => $user->tel,
                    'admin' => $adminText,
                ]);
            }
            return Datatables::of($result)->make(true);
        }
    }

    public function groupMemberAddAdminModal($gid, $uid)
    {
        $group = School::findOrFail($gid);
        if (!$group->isAdmin())
            return 'error';
        if (DB::table('school_user')
            ->whereUserId(Auth::id())
            ->whereSchoolId($gid)
            ->count() == 0)
            return 'error';
        $user = User::findOrFail($uid);
        if (Reg::where('type', 'teamadmin')->where('user_id', $uid)->where('school_id', $gid)->whereNull('conference_id')->count() > 0)
            return view('errorModal', ['msg' => '已是全局管理！']);
        return view('portal.groupMemberAddAdminModal', ['user' => $user, 'group' => $group]);
    }

    public function groupMemberDelAdminModal($gid, $uid)
    {
        $group = School::findOrFail($gid);
        if (!$group->isAdmin())
            return 'error';
        if (DB::table('school_user')
            ->whereUserId(Auth::id())
            ->whereSchoolId($gid)
            ->count() == 0)
            return 'error';
        $user = User::findOrFail($uid);
        $admins = Reg::where('type', 'teamadmin')->where('user_id', $uid)->where('school_id', $gid)->get();
        if ($admins->where('conference_id', null)->count() > 0)
            return view('portal.groupMemberDelGlobalAdminModal', ['user' => $user, 'group' => $group]);
        $admins->load('conference');
        return view('portal.groupMemberDelConfAdminModal', ['user' => $user, 'group' => $group, 'admins' => $admins]);
    }

    public function delAdmin(Request $request)
    {
        $user = User::findOrFail($request->user);
        $group = School::findOrFail($request->group);
        if (!$group->isAdmin())
            return 'error';
        $reg = $request->reg;
        if ($reg == 'all')
        {
            $regs = DB::table('regs')->where('user_id', $user->id)->where('school_id', $group->id)->where('type', 'teamadmin')->pluck('id');
            Teamadmin::destroy($regs);
            Reg::destroy($regs);
        }
        else
        {
            $reg = Reg::findOrFail($reg);
            if ($reg->school_id != $group->id || $reg->type != 'teamadmin')
                return 'error';
            $reg->delete();
        }
        return 'success';
    }

    public function addAdmin(Request $request)
    {
        $user = User::findOrFail($request->user);
        $domain = $request->domain;
        $group = School::findOrFail($request->group);
        $global = (strtolower($domain) == 'global');
        if (!$group->isAdmin())
            return 'error';
        if (DB::table('school_user')
            ->whereUserId(Auth::id())
            ->whereSchoolId($group->id)
            ->count() == 0)
            return 'error';
        if (!$global)
        {
            $conference_id = Cache::tags('domains')->get($domain);
            if (!isset($conference_id))
                $conference_id = DB::table('domains')->where('domain', $domain)->value('conference_id');
            $conference = Conference::find($conference_id);
            if (!is_object($conference))
                return '会议不存在！';
            if ($conference->option('group_disabled'))
		return 'Team disabled in this conference!';
        } else
            $conference_id = null;
        //if (!in_array($conference->status, ['prep', 'reg']))
        //    return '会议未开放报名，不能注册领队！';
        // 假设单场会议单个团队只能有一个 teamadmin
        //$admins = Reg::where('conference_id', $conference->id)->where('school_id', $gid)->where('type', 'teamadmin')->count();
        //if ($admins > 0)
        //    return '本团队在该会议已注册领队，不能重复注册！';
        $new = new Reg;
        $new->user_id = $request->user;
        $new->conference_id = $conference_id;
        $new->school_id = $group->id;
        $new->type = 'teamadmin';
        $new->enabled = 1;
        $new->save();
        $teamadmin = new Teamadmin;
        $teamadmin->reg_id = $new->id;
        $teamadmin->school_id = $group->id;
        $teamadmin->save();
        return 'success';
    }

    public function groupConferences($id)
    {
        $school = School::findOrFail($id);
        if (!$school->isAdmin())
            return 'error';
        return view('portal.teamConferences', ['team' => $school]);
    }

    public function groupConferencesTable($id)
    {
        $user = Auth::user();
        $school = School::findOrFail($id);
        if ($school->isAdmin())
        {
            $result = new Collection;
            $conferences = Conference::whereHas('options', function ($query) use($school) {
                $query->where('school_id', $school->id)
                    ->where('key', 'groupreg_enabled')
                    ->where('value', 1);
            })->withCount(['regs' => function ($query) use($school) {
                $query->where('school_id', $school->id)
                    ->where('type', '!=', 'unregistered');
            }])->get();
            foreach ($conferences as $conference)
            {
                $result->push([
                    'name' => $conference->name,
                    'count' => $conference->regs_count
                ]);
            }
            return Datatables::of($result)->make(true);
        }
    }

    public function groupAddConferenceModal($id)
    {
        $group = School::findOrFail($id);
        if (!$group->isAdmin())
            return 'error';
        return view('portal.groupAddConferenceModal', ['group' => $group]);
    }

    public function groupAddConf(Request $request, $id)
    {
        $group = School::findOrFail($id);
        if (!$group->isAdmin())
            return 'error';
        $domain = $request->domain;
        $conference_id = Cache::tags('domains')->get($domain);
        if (!isset($conference_id))
            $conference_id = DB::table('domains')->where('domain', $domain)->value('conference_id');
        $conference = Conference::find($conference_id);
        if (!is_object($conference))
            return '会议不存在！';
        if ($conference->option('group_disabled'))
            return 'Team disabled in this conference!';
        if ($conference->option('groupreg_enabled', $id))
            return 'Already allowed!';
        $option = new Option;
        $option->conference_id = $conference_id;
        $option->school_id = $id;
        $option->key = 'groupreg_enabled';
        $option->value = 1;
        $option->save();
        return 'success';
    }
}
