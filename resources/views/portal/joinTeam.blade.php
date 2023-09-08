@php
$joinCode = 'null';
if (isset($school)) $joinCode = $school->joinCode;
@endphp
<div class="modal-dialog">
  <div class="modal-content">
    <header class="header bg-info bg-gradient mp-modal-header">
      <center><h4>加入学校社团</h4></center>
    </header>
    <div class="modal-body">
      <div class="row">
        <div class="col-sm-12 b-r">
          <form id="joinForm" class="form-horizontal" data-validate="parsley" action="{{mp_url('/schools/doJoinSchool')}}" method="post">
            {{csrf_field()}}
            <h4>学校信息</h4>
            <div class="form-group">
              <label class="col-sm-2 control-label">学校名称</label>
              <div class="col-sm-10">
                <input id="input-schoolname" type="text" {{isset($school) ? 'disabled=""' : ''}} name="schoolname" class="form-control" value="{{isset($school) ? $school->name : ''}}" data-required="true">
              </div>
              @if (isset($school))
              <input type="hidden" name="schoolid" class="form-control" value="{{$school->id}}">
              @endif
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">社团邀请码</label>
              <div class="col-sm-10">
                <input id="input-schoolcode" type="text" name="code" {{isset($joinCode) ? '' : 'disabled=""'}} class="form-control" placeholder="{{isset($joinCode) ? '可选，由社团管理员提供' : '该社团不允许使用邀请码进入'}}">
              </div>
            </div>
            <h4>社团成员信息</h4>
            <div class="form-group">
              <label class="col-sm-2 control-label">姓名</label>
              <div class="col-sm-10">
                <input type="text" disabled="" name="membername" class="form-control" value="{{Auth::user()->name}}" data-required="true">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">学级</label>
              <div class="col-lg-4 btn-group" data-toggle="buttons">
                <label class="btn btn-sm btn-primary">
                  <input type="radio" name="grade" value="middle"> <i class="fa fa-check text-active"></i> 初中
                </label>
                <label class="btn btn-sm btn-primary">
                  <input type="radio" name="grade" value="high"> <i class="fa fa-check text-active"></i> 高中
                </label>
                <label class="btn btn-sm btn-primary">
                  <input type="radio" name="grade" value="university"> <i class="fa fa-check text-active"></i> 大学
                </label>
              </div>
              <label class="col-sm-2 control-label">毕业年份</label>
              <div class="col-sm-4">
                <input type="text" name="gradeyear" class="form-control" placeholder="您的毕业年份" data-required="true" parsley-type="digit">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">班级</label>
              <div class="col-sm-4">
                <input type="text" name="class" class="form-control" placeholder="可选">
              </div>
              <input type="hidden" name="title" class="form-control" value="新入社员">
            </div>
            <p class="checkbox m-t-lg">
              <a onclick="$('#ajaxModal').modal('hide');$('#ajaxModal').remove();" class="btn btn-sm btn-danger text-uc m-t-n-xs pull-right"><i class="fa fa-times"></i> 取消</a>
              <a onclick="if ($('#joinForm').parsley('validate')){loader(this); $('#joinForm').submit();}" class="btn btn-sm btn-success text-uc m-t-n-xs m-r-xs pull-right"><i class="fa fa-check"></i> 确定</a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div><!-- /.modal-content -->
</div>
<script>
$('#input-schoolcode').change(function(e){
  var e1 = document.getElementById("input-schoolname");
  if (document.getElementById("input-schoolcode") = "") {
    e1.setAttribute("data-required", "true");
  } else {
    e1.setAttribute("data-required", "false");
  }
  $("form").parsley('destroy');
  $("form").parsley();
});
</script>