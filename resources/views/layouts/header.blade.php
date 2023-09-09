<div class="collapse navbar-collapse no-padding">
@if(is_object(Auth::user()))
  <ul class="nav navbar-nav">
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-university text-white"></i>
        <span class="text-white">岐阜县立斐太高等学校</span> <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <li>
          <a href="buttons.html">岐阜县立斐太高等学校</a>
        </li>
        <li class="divider"></li>
        <li>
          <a href="gallery.html">查看我的团队...</a>
        </li>
        <li>
          <a href="gallery.html">查找社团...</a>
        </li>
      </ul>
    </li>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-gavel text-white"></i>
        <span class="text-white">BJMUNC 2022</span> <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <li>
          <a href="dashboard.htm">ROMUNC 2017</a>
        </li>
        <li>
          <a href="dashboard-1.htm">BJMUNSS 2017</a>
        </li>
        <li class="divider"></li>
        <li>
          <a href="gallery.htm">发现会议...</a>
        </li>
      </ul>
    </li>
  </ul>
@endif
  <form class="navbar-form navbar-left m-t-sm" role="search">
    <div class="form-group">
      <div class="input-group input-s">
        <input type="text" class="form-control input-sm no-border dk text-white" placeholder="Search">
        <span class="input-group-btn">
          <button type="submit" class="btn btn-sm btn-primary btn-icon"><i class="fa fa-search"></i></button>
        </span>
      </div>
    </div>
  </form>
  <ul class="nav navbar-nav navbar-right m-r-none">
  @if(is_object(Auth::user()))
    <li class="hidden-xs">
      <a href="#" class="dropdown-toggle dk" data-toggle="dropdown">
        <i class="fa fa-bell fa-lg text-white"></i>
        <span class="badge up bg-danger m-l-n-sm">2</span>
      </a>
      <section class="dropdown-menu animated fadeInUp input-s-lg">
        <section class="panel bg-white">
          <header class="panel-heading">
            <strong>您有 <span class="count-n">2</span> 条新提醒</strong>
          </header>
          <div class="list-group">
            <a href="#" class="media list-group-item">
              <span class="pull-left thumb-sm">
                <img src="images/avatar.jpg" alt="John said" class="img-circle">
              </span>
              <span class="media-body block m-b-none">
                ROMUNC 2017 为您分配了新的面试<br>
                <small class="text-muted">7 分钟前</small>
              </span>
            </a>
            <a href="#" class="media list-group-item">
              <span class="media-body block m-b-none">
                欢迎使用 MUNPANEL<br>
                <small class="text-muted">15 小时前</small>
              </span>
            </a>
          </div>
          <footer class="panel-footer text-sm">
            <a href="#" class="pull-right"><i class="fa fa-cog"></i></a>
            <a href="#">查看全部</a>
          </footer>
        </section>
      </section>
    </li>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle dker" data-toggle="dropdown">
        <span class="thumb-sm avatar pull-left m-t-n-xs m-r-xs">
          <img src="/storage/avatar/{{Auth::user()->avatar}}">
        </span>
        <span>{{Auth::user()->name}}</span>
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <li>
          <a href="profile.html">我的资料</a>
        </li>
        <li>
          <a href="#">设置</a>
        </li>
        <li>
          <a href="/help.html">帮助</a>
        </li>
        <li>
          <a href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" id="logout-submit">退出</a>
        </li>
      </ul>
    </li>
  @else
    <li class="dropdown">
      <a href="/register" class="dker">
        注册新用户 
      </a>
    </li>
    <li class="dropdown">
      <a href="/login" class="dker">
        登录 
      </a>
    </li>
  @endif
  </ul>
  @if(is_object(Auth::user()))
  <form action="/logout" method="post" data-validate="parsley" id="logout-form" style="display: none;">
    {{ csrf_field() }}
  </form>
  <script>

  </script>
  @endif
</div>