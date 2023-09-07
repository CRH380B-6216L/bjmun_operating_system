@extends('layouts.app')
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
        <h3 class="m-t m-b-xs">{{$item}}不存在</h3>
        <p class="block text-muted">您所访问的{{$item}}不存在。</p>
      </div>
    </div>
  </header>
  <div class="scrollable">
  
  </div>  
</section>
     <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen" data-target="#nav"></a>
    </section>
    <!-- /.vbox -->
@endsection
