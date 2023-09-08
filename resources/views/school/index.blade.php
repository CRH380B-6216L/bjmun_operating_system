@php
$user = Auth::user();
if (is_object($user)) $isJoined = $school->users->contains($user) ? true : false;
@endphp
@extends('layouts.app')
@section('teams_active', 'active')
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
  <div class="scrollable">
    <div class="col-lg-9">
      <div class="wrapper bg-light font-bold" style="padding-left: 0px; padding-bottom: 5px;">
        <a href="#" class="m-r"><i class="fa fa-home fa-2x icon-muted v-middle"></i> 主页</a>
        <a href="#" class="m-r"><!--span class="badge up m-r-n bg-danger">4</span--><i class="fa fa-users fa-2x icon-muted v-middle"></i> 成员</a>
        <a href="#" class="m-r"><i class="fa fa-envelope fa-2x icon-muted v-middle"></i> 消息</a>
        <a href="#"><i class="fa fa-cog fa-2x icon-muted v-middle"></i> 管理</a>
      </div>
      <h4>
      社团活动动态
      </h4>
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
      <p>You can use the .hbox and .vbox to build the complicated layouts.</p>  
    </div>           
    <div class="col-lg-3" style="padding-top: 15px">
      <h4>关于社团</h4>
      <p>{{$school->description}}</p>
      <h4>社团操作</h4>
      @if (is_object($user))
        @if ($isJoined) 
        <p>您当前加入了这个社团。</p> 
        <p></p>
        @else
        <p>您还没有加入该社团。</p> 
        <p><a href="/schools/list">加入社团</a></p>
        <p><a href="/schools/join.modal/{{$school->id}}" data-toggle="ajaxModal" >使用团队加入码</a></p>
        @endif
      @else
      <p><strong><a href="/login">登录</a></strong>以加入社团和执行其他操作。</p>  
      @endif
      <div class="clear">
        Powered by BJMUN Operating System<br>Copyright 2009-2022 BJMUN Association<br>All rights reserved
      </div>
    </div>
  </div>  
</section>
     <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen" data-target="#nav"></a>
    </section>
    <!-- /.vbox -->
@endsection
