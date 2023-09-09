@php
$user = Auth::user();
if (is_object($user)) 
{
  $isJoined = $user->schools->contains($school) ? true : false;
  $myStatus = $user->schools->find($school->id)->pivot->status;
}
$managers = $school->managers;
$members = $school->members;
$new = $members->filter(function($x) {return $x->pivot->status == 'pending';})->count();
@endphp
@extends('layouts.app')
@section('teams_active', 'active')
@push('title')
<title>{{$school->name}} - BJMUN Operating System{{config('app.debug')?' - App:Debug':''}}</title>
@endpush
@push('scripts')
<script src="{{cdn_url('js/charts/easypiechart/jquery.easy-pie-chart.js')}}"></script>
<script src="{{cdn_url('/js/fuelux/fuelux.js')}}"></script>
<script src="{{cdn_url('/js/datepicker/bootstrap-datepicker.js')}}"></script>
@endpush
@push('css')
<link href="{{cdn_url('/js/fuelux/fuelux.css')}}" rel="stylesheet">
@endpush
@section('content')
<section class="scrollable bg-light">
  <header>
    <div class="row b-b m-l-none m-r-none lter">
      <div class="col-sm-4">
        <h3 class="m-t m-b-xs">{{$school->name}}</h3>
        <p class="block text-muted">{{$school->typeText()}}模拟联合国社团</p>
      </div>
    </div>
  </header>
  <section class="hbox stretch">
    <div class="col-lg-9">
      <header class="wrapper bg-light font-bold" style="padding-left: 0px; padding-bottom: 5px;">
        <a href="#portal" data-toggle="tab" class="m-r"><i class="fa fa-home fa-2x icon-muted v-middle"></i> 主页</a>
        <a href="#members" data-toggle="tab" class="m-r">
          @if ($new > 0)
          <span class="badge up m-r-n bg-danger">{{$new}}</span>
          @endif
          <i class="fa fa-users fa-2x icon-muted v-middle"></i> 成员</a>
        <a href="#messages" data-toggle="tab" class="m-r"><i class="fa fa-envelope fa-2x icon-muted v-middle"></i> 消息</a>
        <a href="#settings" data-toggle="tab"><i class="fa fa-cog fa-2x icon-muted v-middle"></i> 管理</a>
      </header>
      <section class="vbox scrollable">
        <div class="tab-content">
          <div class="tab-pane active" id="portal">
            <h4>社团活动动态</h4>
            <div class="post-item">
              <div class="caption wrapper-lg">
                <h2 class="post-title"><a href="#">笠松特雷森模联管理层换届通告</a></h2>
                <div class="post-sum">
                  <p>2017 年 6 月 1 日，笠松特雷森模拟联合国协会完成了管理层的换届工作，8 名 G16 级的同学正式成为新一任管理层成员。值此向 G15 级管理层及所有为社团发展辛勤付出的同学表示敬意和肯定，也期望在未来的一年中笠松模联社团能够再接再厉，以新的姿态迎接重重挑战。</p>
                  <p><a href="#">查看全部</a></p>
                </div>
                <div class="line line-lg"></div>
                <div class="text-muted">
                  <i class="fa fa-user icon-muted"></i> by <a class="m-r-sm" href="#">国家赛马娘训练中心附属笠松中学</a>
                  <i class="fa fa-clock-o icon-muted"></i> 2017 年 6 月 1 日
                  <a class="m-l-sm" href="#"><i class="fa fa-comment-o icon-muted"></i> 10 comments</a>
                </div>
              </div>
            </div>
          </div><!--tab-pane#portal-->
          <div class="tab-pane" id="members">
            <div class="col-xs-12" style="padding: 0">
              <h4>社团负责人</h4>
              @foreach ($managers as $member)
              <p class="col-xs-6 col-sm-4 col-md-3">
                <span class="pull-left thumb-sm avatar m-r-sm m-t-xs">
                  <img class="img-circle" src="/storage/avatar/{{$member->avatar}}">
                </span>
                <span class="h5"><a href="#"><strong>{{$member->name}}</strong>{{$member->id == Auth::id() ? '（我）' : ''}}</a></span><br>
                <small class="text-muted">{{$member->pivot->class}}<br>
                {{$member->pivot->title}}</small>
              </p>
              @endforeach
              </div>
              <div class="col-xs-12" style="padding: 0">
              <h4>社团成员 <small>成员总数: {{$managers->count() + $members->count()}}</small></h4>
              @foreach ($members as $member)
              <p class="col-xs-6 col-sm-4 col-md-3">
                <span class="pull-left thumb-sm avatar m-r-sm m-t-xs">
                  <img class="img-circle" src="/storage/avatar/{{$member->avatar}}">
                </span>
                <span class="h5"><a href="#"><strong>{{$member->name}}</strong>{{$member->id == Auth::id() ? '（我）' : ''}}</a></span><br>
                <small class="text-muted">{{$member->pivot->class}}<br>
                {{$member->pivot->title}}</small>
              </p>
              @endforeach
            </div>
          </div><!--tab-pane#members-->
          <div class="tab-pane" id="messages">
            <p>under construction</p>
          </div><!--tab-pane#messages-->
          <div class="tab-pane" id="settings">
            <p>under construction</p>
          </div><!--tab-pane#settings-->
        </div>
      </section><!--.scrollable.tab-contents-->
    </div>           
    <div class="col-lg-3" style="padding-top: 15px">
      <h4>关于社团</h4>
      <p>{{$school->description}}</p>
      <h4>社团操作</h4>
        @if (is_object($user))
          @if ($isJoined) 
            @if ($myStatus == 'master')
            <p>您是该社团的社长，可以对所有社团成员进行管理操作并添加或删除管理权限。</p>
            <p>在“成员”面板点击成员姓名以执行管理操作。</p>
            <p><a href="#" data-toggle="ajaxModal">变更个人信息</a></p>
            <p><a href="#" data-toggle="ajaxModal">修改社团信息</a></p>
            <p><a href="#" data-toggle="ajaxModal">移交社长身份</a></p>
            @elseif ($myStatus == 'officer')
            <p>您是该社团的管理人员，可以对普通社团成员进行管理操作。</p>
            <p>在“成员”面板点击成员姓名以执行管理操作。</p>
            <p><a href="#" data-toggle="ajaxModal">变更个人信息</a></p>
            <p><a href="#" data-toggle="ajaxModal">从社团退休</a></p>
            <p><a href="#" data-toggle="ajaxModal">退出社团</a></p>
            @elseif ($myStatus == 'active')
            <p>您当前加入了这个社团。</p> 
            <p><a href="#" data-toggle="ajaxModal">变更个人信息</a></p>
            <p><a href="#" data-toggle="ajaxModal">从社团退休</a></p>
            <p><a href="#" data-toggle="ajaxModal">退出社团</a></p>
            @elseif ($myStatus == 'pending')
            <p>您已申请加入该社团，请等待社团负责人审核。</p> 
            <p><a href="#" data-toggle="ajaxModal">放弃申请</a></p>
            @elseif ($myStatus == 'retired')
            <p>您已从该社团退休。</p> 
            @elseif ($myStatus == 'delisted')
            <p>您已被该社团除名，无法进行任何操作。</p> 
            @endif
          <p></p>
          @else
          <p>您还没有加入该社团。</p> 
          <p><a href="/schools/join.modal/{{$school->id}}" data-toggle="ajaxModal">加入社团</a></p>
          @endif
        @else
        <p><strong><a href="/login">登录</a></strong>以加入社团和执行其他操作。</p>  
        @endif
      <div class="clear">
        Powered by BJMUN Operating System<br>Copyright 2009-2022 BJMUN Association<br>All rights reserved
      </div>
    </div>  
  </section>
</section>
@endsection
