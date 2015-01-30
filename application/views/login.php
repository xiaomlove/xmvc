<div class="container">
    <form class="form-horizontal" role="form" id="login-form" action="<?php echo $this->createUrl('index/home')?>" autocomplete="off" method="post">
      <div class="form-group">
        <label for="inputName" class="col-sm-2 control-label">账号</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="inputName" name="username" placeholder="账号" autocomplete="off">
          <span class="help-block hide">A block of help text that brea.</span>
        </div>
      </div>
      <div class="form-group">
        <label for="inputPassword" class="col-sm-2 control-label">密码</label>
        <div class="col-sm-10">
          <input type="password" class="form-control" id="inputPassword" name="password" placeholder="密码" autocomplete="off">
          <span class="help-block hide">A block of help text that brea.</span>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="submit" class="btn btn-success" id="login-submit">Sign in</button>
          <span class="text-success go-register">没有账号？<a href="<?php echo $this->createUrl('index/register')?>">去注册</a></span>
        </div>
      </div>
    </form>
  </div>

  <script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/js/login.js"></script>
  <script type="text/javascript">
  var $name = $("#inputName"), $password = $("#inputPassword"),$submit = $("#login-submit");
  $name.on("input", function() {
  if($(this).hasClass("invalid")){
        validateName($(this));
      }  
  })

  $password.on("input", function() {
    if($(this).hasClass("invalid")){
      validatePassword($(this));
    }
  })

  $submit.on("click", function(e) {
    if(!validateName($name) || !validatePassword($password)) {
    	e.preventDefault();
    }
  })
  </script>