@extends('layouts.app')
@section('home_active', 'active')
@push('title')
<title>BJMUN Operating System{{config('app.debug')?' - App:Debug':''}}</title>
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
        <h3 class="m-t m-b-xs">主页</h3>
        <p class="block text-muted">欢迎使用 BJMUN Operating System</p>
      </div>
    </div>
    <div class="wrapper bg-light font-bold">
      <a href="#" class="m-r"><i class="fa fa-chart-bar fa-2x icon-muted v-middle"></i> Analysis</a>
      <a href="#" class="m-r"><span class="badge up m-r-n bg-danger">4</span><i class="fa fa-envelope fa-2x icon-muted v-middle"></i> Message</a>
      <a href="#" class="m-r"><i class="fa fa-calendar fa-2x icon-muted v-middle"></i> My Calendar</a>
      <a href="#"><i class="fa fa-cog fa-2x icon-muted v-middle"></i> Settings</a>
    </div>
  </header>
  <div class="scrollable">
    <div class="col-lg-9">
      <div class="panel-group m-b" id="accordion2">
        <h4>BJMUN 公告栏</h4>
        <div class="panel">
          <div class="panel-heading v-middle">
            <span class="pull-right text-sm">1 小时前</span>
            <a class="accordion-toggle h4" aria-expanded="true" href="#collapseOne" data-toggle="collapse" data-parent="#accordion2">
              团队注册问题已修复
            </a>
          </div>
          <div class="panel-collapse collapse in" id="collapseOne" aria-expanded="true">
            <div class="panel-body">
              <p><strong>发布者: </strong>adamyi<br><strong>发布于: </strong>2017-6-6 16:12:18 (1 小时前)<br><strong>过期时间: </strong>2017-6-13 12:00:00 (7 天后)</p>
              <p class="m-b-none">十分抱歉由于一些问题，在 6 月 5 日 13:20 前注册的团队系统并未成功添加默认全局管理身份，而提示错误信息，目前问题已得到解决。先前注册的团队的默认全局管理身份已全部得以添加，因此问题而重复注册的团队已被删除，感谢您的理解与支持。</p>
            </div>
          </div>
        </div>
        <div class="panel">
          <div class="panel-heading v-middle">
            <span class="pull-right text-sm">3 小时前</span>
            <a class="accordion-toggle h4 collapsed" aria-expanded="false" href="#collapse2" data-toggle="collapse" data-parent="#accordion2">
              欢迎使用 BJMUN Operating System！
            </a>
          </div>
          <div class="panel-collapse collapse collapsed" id="collapse2" aria-expanded="false">
            <div class="panel-body">
              <p><strong>发布者: </strong>adamyi<br><strong>发布于: </strong>2017-6-6 13:59:16 (3 小时前)<br><strong>过期时间: </strong>2017-8-6 14:00:00 (2 个月后)</p>
              <p>This is a portal page under development... Later, you will be able to modify your personal info like name, email, tel, etc., check all the conferences you have registered before, and even find some new conferences that may interest you.</p>
            </div>
          </div>
        </div>
        <div class="panel">
          <div class="panel-heading">
            <a class="accordion-toggle collapsed" href="announcements"  data-parent="#accordion2">
              查看历史公告...
            </a>
          </div>
        </div>
      </div>              
      <h4>
      还不知道这里放什么
      </h4>
      <p>You can use the .hbox and .vbox to build the complicated layouts.</p>  
    </div>           
    <div class="col-lg-3">
      <h4>
      还不知道这里放什么
      </h4>              
      <p>You can use the .hbox and .vbox to build the complicated layouts. </p><p>暂且放四个按钮意思意思</p>  
      <div class="font-bold">
        <p><a class="m-r" href="#"><i class="fa fa-chart-bar fa-2x icon-muted v-middle"></i> Analysis</a></p>
        <p><a class="m-r" href="#"><span class="badge up m-r-n bg-danger">4</span><i class="fa fa-envelope fa-2x icon-muted v-middle"></i> Message</a></p>
        <p><a class="m-r" href="#"><i class="fa fa-calendar fa-2x icon-muted v-middle"></i> My Calendar</a></p>
        <p><a href="#"><i class="fa fa-cog fa-2x icon-muted v-middle"></i> Settings</a></p>
      </div>
      <div class="clear">
        Powered by BJMUN Operating System<br>Copyright 2009-2022 BJMUN Association<br>All rights reserved</a>
      </div>
    </div>
  </div>  
</section>
     <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen" data-target="#nav"></a>
    </section>
    <!-- /.vbox -->
@endsection
