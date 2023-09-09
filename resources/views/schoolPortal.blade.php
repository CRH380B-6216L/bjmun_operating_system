@php
$user = Auth::user();
$count = 0;
if (is_object($user))
{
  $schools = $user->schools;
  if (is_object($schools)) $count = $schools->count();
}
@endphp
@extends('layouts.app')
@section('teams_active', 'active')
@push('title')
<title>社团与团队 - BJMUN Operating System{{config('app.debug')?' - App:Debug':''}}</title>
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
        <h3 class="m-t m-b-xs">社团与团队</h3>
        <p class="block text-muted">查找、加入或创建你的学校社团</p>
      </div>
    </div>
  </header>
  <div class="">
    <div class="col-lg-9">
      <h4>社团活动动态 <small>已选区域: 北京</small></h4>
      <div class="post-item">
          <div class="caption wrapper-lg">
            <h2 class="post-title"><a href="#">清华附中模联管理层换届通告 </a></h2>
            <div class="post-sum">
              <p>2017 年 6 月 1 日，清华大学附属中学模拟联合国协会完成了管理层的换届工作，8 名 G16 级的同学正式成为新一任管理层成员。值此向G15级管理层及所有为社团发展辛勤付出的同学表示敬意和肯定，也期望在未来的一年中附中模联社团能够再接再厉，以新的姿态迎接重重挑战。</p>
              <p><a href="#">查看全部</a></p>
            </div>
            <div class="line line-lg"></div>
            <div class="text-muted">
              <i class="fa fa-user icon-muted"></i> by <a class="m-r-sm" href="#">清华大学附属中学</a>
              <i class="fa fa-clock-o icon-muted"></i> 2017 年 6 月 1 日
              <a class="m-l-sm" href="#"><i class="fa fa-comment-o icon-muted"></i> 10 comments</a>
            </div>
          </div>
        </div>
      <div class="post-item">
          <div class="caption wrapper-lg">
            <h2 class="post-title"><a href="#">任神高模联社长的心得</a></h2>
            <div class="post-sum">
              <p>大家好，我是神高模联社社长千反田える。我就任神高模联社社长到现在已经有一年时间了，在这一年中我带领社员参加了十余场会议，我的社员也多次获得奖项。在这篇文章中我将向大家分享我任社长这一年来积累的一些经验。</p>
              <p><a href="#">查看全部</a></p>
            </div>
            <div class="line line-lg"></div>
            <div class="text-muted">
              <i class="fa fa-user icon-muted"></i> by <a class="m-r-sm" href="#">神高模联社 / 千反田える</a>
              <i class="fa fa-clock-o icon-muted"></i> 2017 年 5 月 20 日
              <a class="m-l-sm" href="#"><i class="fa fa-comment-o icon-muted"></i> 2 comments</a>
            </div>
          </div>
        </div>
    </div>
    <div class="col-lg-3">
      <h4>我的社团</h4>
      @if (is_object($user))
      <p>您当前加入了 {{$count}} 个社团。</p> 
      @if ($count > 0) 
        @foreach ($schools as $school)
        <p>
          <span class="pull-left thumb-sm avatar m-r-sm m-t-xs">
            <img class="img-circle" src="images/avatar.jpg">
          </span>
          <span class="h5"><strong><a href="/school/{{$school->id}}">{{$school->name}}</a></strong></span><br>
          <small class="text-muted">{{$school->typeText()}}模拟联合国社团<br>
          您的身份: {{$school->pivot->title}}</small>
        </p>
        @endforeach
      <p><a href="#">查看我曾经加入的社团</a></p>
      @else
      <p><a href="/schools/list">加入社团</a></p>
      <p><a href="/schools/new.modal" data-toggle="ajaxModal" >创建社团</a></p>
      @endif
      <p><a href="#">完善我的学校信息</a></p>
      <p><a href="/schools/join.modal" data-toggle="ajaxModal" >使用团队加入码</a></p>
      @else
      <p><strong><a href="/login">登录</a></strong>以创建、加入或管理您的社团。</p>  
      @endif
      <div class="clear">
        Powered by BJMUN Operating System<br>Copyright 2009-2022 BJMUN Association<br>All rights reserved 
        <br><a title="京ICP备17022496号-1" href="http://www.miibeian.gov.cn/" rel="nofollow">京ICP备17022496号-1</a>
      </div>
    </div>
  </div>  
</section>
@endsection