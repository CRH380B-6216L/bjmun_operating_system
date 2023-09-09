@php

@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('title')
    <meta name="keywords" content="BJMUN,MUN,Model UN,Model United Nations,United Nations,UN,PANEL,模联,模拟联合国">
    <meta name="copyright" content="Proudly Powered and Copyrighted by {{config('bjmun.copyright_year')}} BJMUN.">
    <meta name="generator" content="BJMUN Operating System">
    <meta name="author" content="Adam Yi">


    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="theme-color" content="#5dcff3">
    <link rel="author" href="humans.txt" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{cdn_url('/css/bootstrap.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{cdn_url('/css/animate.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{cdn_url('/css/font-awesome.min.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{cdn_url('/css/font.css')}}" type="text/css" cache="false" />
    <link rel="stylesheet" href="{{cdn_url('/css/plugin.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{cdn_url('/css/app.css')}}" rel="stylesheet" />
    @stack('css')
    <link rel="stylesheet" href="{{cdn_url('/css/bjmun-os.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{mp_url('/css/maia.footer.css')}}" type="text/css" />

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <!--[if lt IE 9]>
      <script src="{{cdn_url('/js/ie/respond.min.js')}}" cache="false"></script>
      <script src="{{cdn_url('/js/ie/html5.js')}}" cache="false"></script>
      <script src="{{cdn_url('/js/ie/excanvas.js')}}" cache="false"></script>
      <script src="{{cdn_url('/js/ie/fix.js')}}" cache="false"></script>
    <![endif]-->
    @include('layouts.analytics')
</head>
<body>
  <section class="hbox stretch">
    <!-- .aside -->
    <aside class="aside bg-dark dk @yield('hide_aside')" id="nav">
      <section class="vbox">
        <header class="dker nav-bar nav-bar-fixed-top">
          <div class="navbar-header nav-bar aside dk">
            <a class="btn btn-link visible-xs" data-toggle="class:show" data-target=".nav-primary">
              <i class="fa fa-bars"></i>
            </a>
            <a href="#" class="nav-brand" data-toggle="fullscreen">BJMUN OS</a>
            <a class="btn btn-link visible-xs" data-toggle="collapse" data-target=".navbar-collapse">
              <i class="fa fa-comment-o"></i>
            </a>
          </div>
        </header>
        <section class="scrollable">
          <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px">
          <!-- user -->
          <!--div class="bg-success nav-user hidden-xs pos-rlt">
            <div class="nav-avatar pos-rlt">
              <a href="#" class="thumb-sm avatar animated rollIn" data-toggle="dropdown">
                <img src="{{ 'https://www.gravatar.com/avatar/' . md5( strtolower( trim( Auth::check() ? Auth::user()->email : 'official@bjmun.org' ) ) ) . '?d='.mp_url('images/avatar.png').'&s=320' }}" alt="" class="">
                <span class="caret caret-white"></span>
                @if (config('app.debug'))
                CONFIDENTIAL
                @endif
              </a>
              <ul class="dropdown-menu m-t-sm animated fadeInLeft">
              	<span class="arrow top"></span>
                <--li>
                  <a href="#">Settings</a>
                </li>
                <li>
                  <a href="profile.html">Profile</a>
                </li>
                <li>
                  <a href="#">
                    <span class="badge bg-danger pull-right">3</span>
                    Notifications
                  </a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="help.html">Help</a>
                </li-- >
                
              </ul>
              
            </div>
            <!--div class="nav-msg">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <b class="badge badge-white count-n">2</b>
              </a>
              <section class="dropdown-menu m-l-sm pull-left animated fadeInRight">
                <div class="arrow left"></div>
                <section class="panel bg-white">
                  <header class="panel-heading">
                    <strong>You have <span class="count-n">2</span> notifications</strong>
                  </header>
                  <div class="list-group">
                    <a href="#" class="media list-group-item">
                      <span class="pull-left thumb-sm">
                        <img src="images/avatar.jpg" alt="John said" class="img-circle">
                      </span>
                      <span class="media-body block m-b-none">
                        MUNPANEL is awesome<br>
                        <small class="text-muted">30 Jan 16</small>
                      </span>
                    </a>
                    <a href="#" class="media list-group-item">
                      <span class="media-body block m-b-none">
                        Hello World!<br>
                        <small class="text-muted">30 Jan 16</small>
                      </span>
                    </a>
                  </div>
                  <footer class="panel-footer text-sm">
                    <a href="#" class="pull-right"><i class="fa fa-cog"></i></a>
                    <a href="#">See all the notifications</a>
                  </footer>
                </section>
              </section>
            </div>
          </div-->
          <!-- / user -->
          <!-- nav -->
          <nav class="nav-primary hidden-xs">
            <ul class="nav">
            @if (config('app.debug'))
              <li class="bg-danger @yield('debug_active')">
                <a style="color: #ffffff;" href="{{ mp_url('/aboutDebug') }}">
                  <i class="fa fa-bug"></i>
                  <span>Dev Mode</span>
                </a>
              </li>
              @endif
              @if (false) <!--(is_object(Reg::current()) && Reg::current()->user_id != Auth::id())-->
              <li class="bg-warning @yield('sudo_active')">
                <a href="{{ mp_url('/aboutSudo') }}">
                  <i class="fa fa-address-card"></i>
                  <span>SUDOing</span>
                </a>
              </li>
              @endif
              @include('layouts.portal')
              <li>
                <a href="conferences">
                  <i class="fa fa-gavel"></i>
                  <span>Conference</span>
                </a>
              </li>
              <li>
                <a href="mail.html">
                  <b class="badge bg-primary pull-right">3</b>
                  <i class="fa fa-mail-bulk"></i>
                  <span>Mail</span>
                </a>
              </li>
              <li>
                <a href="tasks.html">
                  <i class="fa fa-tasks"></i>
                  <span>Tasks</span>
                </a>
              </li>
              <li>
                <a href="notes.html">
                  <i class="fa fa-book"></i>
                  <span>Archive</span>
                </a>
              </li>
              <li>
                <a href="community.html">
                  <i class="fa fa-comments"></i>
                  <span>Community</span>
                </a>
              </li>
                @if (false)<!--(Reg::currentConferenceID() != 0 && is_object(Reg::current()) && Reg::current()->type != 'teamadmin')-->
                  @foreach(Auth::user()->regs->where('conference_id', Reg::currentConferenceID())->where('enabled', true) as $reg)
                  @if ($reg->type == 'teamadmin')
                  <li class="@yield('interview_active')">
                    <a href="{{ mp_url('/doSwitchIdentity/'.$reg->id) }}">
                      <i class="fa fa-university"></i>
                      <span>{{$reg->school->name}}报名管理</span>
                    </a>
                  </li>
                  @endif
                  @endforeach
                @endif 
                <li class="@yield('store_active')">
                  <a href="{{ mp_url('/store') }}">
                    <i class="fa fa-shopping-bag"></i>
                    <span>Store</span>
                  </a>
                </li>
              </ul>
          </nav>
          <!-- / nav -->
          {{--<!-- note -->
          <div class="bg-primary wrapper hidden-vertical animated fadeInUp text-sm">
              <!--a href="#" data-dismiss="alert" class="pull-right m-r-n-sm m-t-n-sm"><i class="fa fa-times"></i></a-->
              Proudly Powered and Copyrighted by {{config('munpanel.copyright_year')}} MUNPANEL.
              @if(null !== config('munpanel.icp_license'))
              &nbsp;{{config('munpanel.icp_license')}}
              @endif
          </div>
          <!-- / note -->--}}
          </div>
        </section>
        <footer class="footer bg-gradient hidden-xs">
          <a href="{{mp_url('/selectIdentityModal')}}" data-toggle="ajaxModal" class="btn btn-sm btn-link m-r-n-xs pull-right">
            <i class="fa fa-power-off"></i>
          </a>
          <a href="#nav" data-toggle="class:nav-vertical" class="btn btn-sm btn-link m-l-n-sm">
            <i class="fa fa-bars"></i>
          </a>
        </footer>
      </section>
    </aside>
    <!-- /.aside -->
  <section class="vbox">
      <header class="header bg-black navbar no-padding">
        @include('layouts.header')
      </header>
      <section class="scrollable bg-light" id="content">
        @yield('content')
      </section>
      <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen" data-target="#nav"></a>
    </section>
    <!-- /.vbox -->
  </section>
  <div class="maia-footer" id="maia-footer">
  {{--<div id="maia-footer-local">
  <div class="maia-aux">
  <div class="policy-footer-sitemap">
  <div class="maia-cols">
  <div class="maia-col-3">
  <h3>Our legal policies</h3>
  <ul>
  <li>Privacy Policy
  </li><li><a href="../../policies/terms/">Terms of Service</a>
  </li><li><a href="../../policies/faq/">FAQ</a></li></ul></div>
  <div class="maia-col-3">
  <h3>More information</h3>
  <ul>
  <li><a href="../../policies/technologies/">Technologies and Principles</a>
  </li><li><a href="../../policies/technologies/ads/">Advertising</a>
  </li><li><a href="../../policies/technologies/cookies/">How Google uses cookies</a>
  </li><li><a href="../../policies/technologies/pattern-recognition/">How Google uses pattern recognition</a>
  </li><li><a href="../../policies/technologies/location-data/">Types of location data used by Google</a></li></ul></div>
  <div class="maia-col-3" id="more-information-continued">
  <ul>
  <li><a href="../../policies/technologies/wallet/">How Google Wallet uses credit card numbers</a>
  </li><li><a href="../../policies/technologies/voice/">How Google Voice works</a>
  </li><li><a href="../../policies/privacy/partners/">How Google uses data when you use our partners' sites or apps</a></li></ul></div>
  <div class="maia-col-3">
  <h3>Additional resources</h3>
  <ul>
  <li><a href="https://myaccount.google.com?hl=en">My Account</a>
  </li><li><a href="../../safetycenter/families/start/">Google Safety Center</a>
  </li><li><a href="../../policies/technologies/product-privacy/">Google Product Privacy Guide</a>
  </li><li><a href="https://privacy.google.com?hl=en">Your privacy, security, and controls</a></li></ul></div></div></div></div></div>--}}
  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> {{ csrf_field() }} </form>
  <script src="{{cdn_url('/js/jquery.min.js')}}"></script>
  <!-- Bootstrap -->
  <script src="{{cdn_url('/js/bootstrap.min.js')}}"></script>
  <!-- App -->
  <script src="{{cdn_url('/js/app.js')}}"></script>
  <script src="{{cdn_url('/js/app.plugin.js')}}"></script>
  <script src="{{cdn_url('/js/app.data.js')}}"></script>
  <script src="{{cdn_url('js/slimscroll/jquery.slimscroll.min.js')}}" cache="false"></script>
  <!-- Parsley -->
  <script src="{{cdn_url('/js/parsley/parsley.min.js')}}"></script>
  <script src="{{cdn_url('/js/parsley/parsley.extend.js')}}"></script>
  @stack('scripts')
  @if (isset($notice_msg))
  <script>
                var $modal = $('<div class="modal" id="ajaxModal"><div class="modal-body"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><div class="row"><div class="col-sm-12 b-r"><div class="alert alert-danger"><b>{{$notice_msg}}</b></div></div></div></div></div></div></div></div>');
                $('body').append($modal);
                $modal.modal();
  </script>
  @endif
  @if (isset($initialModal))
  <script>
        var $modal = $('<div class="modal" id="ajaxModal"><div class="modal-body"></div></div>');
        $('body').append($modal);
        $modal.modal();
        $modal.load("{{$initialModal}}");
  </script>
  @endif
  <script>setInterval(function(){var e=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject('Microsoft.XMLHTTP');e.open('GET','{{mp_url('/keepalive')}}',!0);e.setRequestHeader('X-Requested-With','XMLHttpRequest');e.send();}, 1200000);</script>
</body>
</html>

