<div class="modal-dialog">
  <div class="modal-content">
    <header class="header bg-info bg-gradient mp-modal-header">
      <center><h4>新建学校社团</h4></center>
    </header>
    <div class="modal-body">
      <div class="row">
        <div class="col-sm-12 b-r">
          <form id="newForm" class="form-horizontal" data-validate="parsley" action="{{mp_url('/schools/doCreateSchool')}}" method="post">
            {{csrf_field()}}
            <h4>学校信息</h4>
            <div class="form-group">
              <label class="col-sm-2 control-label">学校名称</label>
              <div class="col-sm-10">
                <input type="text" name="name" class="form-control" placeholder="如 Massachusetts Institute of Technology" data-required="true">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">学校类型</label>
              <div class="col-lg-6 btn-group" data-toggle="buttons">
                <label class="btn btn-sm btn-primary">
                  <input type="radio" name="type" value="school"> <i class="fa fa-check text-active"></i> 中学
                </label>
                <label class="btn btn-sm btn-primary">
                  <input type="radio" name="type" value="university"> <i class="fa fa-check text-active"></i> 高等学校
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">学校简介</label>
              <div class="col-sm-10">
                <input type="text" name="description" class="form-control" placeholder="其他用户将可看到此简介" data-required="true">
              </div>
            </div>
            <h4>社团管理员信息</h4>
            <p>新社团的创建者将自动成为该社团的社长。</p>
            <div class="form-group">
              <label class="col-sm-2 control-label">姓名</label>
              <div class="col-sm-10">
                <input type="text" disabled="" name="mastername" class="form-control" value="{{Auth::user()->name}}" data-required="true">
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
              <label class="col-sm-2 control-label">身份</label>
              <div class="col-sm-4">
                <input type="text" name="title" class="form-control" value="社长">
              </div>
            </div>
            <p class="checkbox m-t-lg">
              <a onclick="$('#ajaxModal').modal('hide');$('#ajaxModal').remove();" class="btn btn-sm btn-danger text-uc m-t-n-xs pull-right"><i class="fa fa-times"></i> 取消</a>
              <a onclick="if ($('#newForm').parsley('validate')){loader(this); $('#newForm').submit();}" class="btn btn-sm btn-success text-uc m-t-n-xs m-r-xs pull-right"><i class="fa fa-check"></i> 确定</a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div><!-- /.modal-content -->
</div>
