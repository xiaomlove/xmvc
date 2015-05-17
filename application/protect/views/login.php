<div class="container">
    <form class="form-horizontal" role="form" id="login-form" action="checklogin" autocomplete="off" method="post">
      <div class="form-group">
        <label for="inputName" class="col-sm-2 control-label">账号</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="inputName" name="name" placeholder="账号" autocomplete="off">
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
          <button type=button class="btn btn-success" id="login-submit">Sign in</button>
          <span class="text-success go-register">没有账号？<a href="<?php echo $this->createUrl('index/register')?>">去注册</a></span>
          &nbsp;&nbsp;<span class="text-warning">测试账号：111111，密码：111111</span>
          &nbsp;<strong class="text-info"><a href="/about">【关于】</a></strong>
        </div>
      </div>
    </form>
  </div>

  <script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
  <script src="<?php echo framework\App::ins()->request->getBaseUrl()?>application/assets/js/login.js"></script>
  <script type="text/javascript">
  var $name = $("#inputName"), $password = $("#inputPassword"),$submit = $("#login-submit");
	var $form = $("#login-form");
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
    if(validateName($name) && validatePassword($password)) {
    	$.ajax({
			url: "checklogin",
			type: "POST",
			dataType: "json",
			data: $form.serialize(),
			beforeSend: function(){
				$submit.attr("disabled", "disabled").text("登陆中...");
			},
			success: function(data){
				switch(data.code){
					case 0:
						_showError($name, data.msg, true);
						$submit.removeAttr("disabled").text("Sign in");
						break;
					case -1:
						_showError($name, data.msg, true);
						$submit.removeAttr("disabled").text("Sign in");
						break;
					case -2:
						_showError($password, data.msg, true);
						$submit.removeAttr("disabled").text("Sign in");
						break;
					case -3:
						_showError($name, data.msg, true);
						$submit.removeAttr("disabled").text("Sign in");
						break;
					case 1:
						window.location.href = "index.html"
				}
			}
        })
    }
  })
 
  </script>