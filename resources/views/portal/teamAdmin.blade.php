@extends('layouts.app')
@section('teams_active', 'active')
@section('content')
<div class="container">
    <div class="row"><br/><br/><br/></div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{$team->name}} - Control Panel (Under Development)</div>

                <div class="panel-body">
                    您的团队加入码为：
                    <div id="hiddenCode">********************************</div>
                    <div id="shownCode" style="display:none;">{{$team->joinCode}}</div>
                    请告知欲加入您的团队的人此邀请码。<br>
                    <button id="showButton" type="button" class="btn btn-danger" onclick="$('#showButton').hide(); $('#hideButton').show(); $('#hiddenCode').hide(); $('#shownCode').show();">显示加入码</button>
                    <button id="hideButton" style="display:none;" type="button" class="btn btn-danger" onclick="$('#showButton').show(); $('#hideButton').hide(); $('#hiddenCode').show(); $('#shownCode').hide();">隐藏加入码</button>
                    This is a place for you to manage your team members, team admins, and join codes. 其他功能开发中
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
