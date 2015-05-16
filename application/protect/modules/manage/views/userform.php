<h3 class="main-title">
	<strong>添加用户</strong>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/user/userlist')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>返回列表</a>
</h3>

<form class="form-horizontal">
  <div class="form-group">
    <label for="name" class="col-sm-2 control-label">用户名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="name" name="name" placeholder="">
    </div>
  </div>
  <div class="form-group">
    <label for="email" class="col-sm-2 control-label">邮箱</label>
    <div class="col-sm-10">
      <input type="email" class="form-control" id="email" name="email" placeholder="">
    </div>
  </div>
  
  <div class="form-group">
    <label for="password" class="col-sm-2 control-label">密码</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="password" name="password" placeholder="">
    </div>
  </div>
  
  <div class="form-group">
    <label for="password2" class="col-sm-2 control-label">确认密码</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="password2" name="password2" placeholder="">
    </div>
  </div>
  
  
  <div class="form-group">
    <div class="col-sm-offset-6 col-sm-6">
      <button type="submit" class="btn btn-primary">确定</button>
    </div>
  </div>
</form>
