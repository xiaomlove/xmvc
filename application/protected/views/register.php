<div class="container">
    <form class="form-horizontal" role="form" id="register-form" action="checkRegister" method="post">
      <div class="form-group">
        <label for="inputName" class="col-sm-2 control-label">账号</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="inputName" name="username" placeholder="账号">
          <span class="help-block hide">A block of help text that brea.</span>
        </div>

      </div>
      <div class="form-group">
        <label for="inputPassword" class="col-sm-2 control-label">密码</label>
        <div class="col-sm-10">
          <input type="password" class="form-control" name="password" id="inputPassword" placeholder="密码">
          	<span class="help-block hide">A block of help text that brea.</span>
          </div>
          
      </div>
      <div class="form-group">
        <label for="inputPassword2" class="col-sm-2 control-label">确认密码</label>
        <div class="col-sm-10">
          <input type="password" class="form-control" name="password2" id="inputPassword2" placeholder="确认密码">
          	<span class="help-block hide">A block of help text that brea.</span>
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail" class="col-sm-2 control-label">邮箱</label>
        <div class="col-sm-10">
          <input type="email" class="form-control" name="email" id="inputEmail" placeholder="邮箱">
          <span class="help-block hide">A block of help text that brea.</span>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="button" class="btn btn-success" id="register-submit">Register</button>
          <span class="text-success go-login"><span id="result-msg">已有账号？</span><a href="<?php echo $this->createUrl('index/login')?>">去登陆</a></span>
        </div>
      </div>
    </form>
  </div>

  <script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/js/login.js"></script>
  <script>

  var $name = $("#inputName"), $password = $("#inputPassword"), $password2 = $("#inputPassword2"), $email = $("#inputEmail"), $submit = $("#register-submit");
	var $form = $("#register-form");
  $name.on("input", function() {
   	if($(this).hasClass("invalid")){
  			validateName($(this));
 		}  
  })

  $email.on("input", function() {
 	if($(this).hasClass("invalid")){
  		validateEmail($(this));
 	}
  })

  $password.on("input", function() {
   if($(this).hasClass("invalid")){
    	validatePassword($(this));
   }
  })

  $password2.on("input", function() {
   if($(this).hasClass("invalid")){
    	if(validatePassword($(this))){
    		if($(this).val() !== $password.val()){
    			_showError($(this), "密码不一致");
    		}else{
        		_hideError($(this));
    		}
    	}
   }
  })

  $submit.on("click", function(e) {
    if(!validateName($name, true) || 
    	!validatePassword($password, true) || 
    	!validatePassword2($password2, $password, true) || 
    	!validateEmail($email, true)) {
    }else{
       $.ajax({
			url: "checkRegister",
			type: "POST",
			dataType: "json",
			data: $form.serialize(),
			beforeSend: function(){
					$submit.attr("disabled", "disabled").text("注册中...");
				},
			success: function(data){
				switch(data.code){
					case 0:
						_showError($name, data.msg, true);
						$submit.removeAttr("disabled").text("Register");
						break;
					case -1:
						_showError($name, data.msg, true);
						$submit.removeAttr("disabled").text("Register");
						break;
					case -2:
						console.log('-2');
						_showError($email, data.msg, true);
						$submit.removeAttr("disabled").text("Register");
						break;
					case -3:
						_showError($name, data.msg, true);
						$submit.removeAttr("disabled").text("Register");
						break;
					case 1:
						$("#result-msg").text(data.msg).addClass("text-danger");
						$submit.text("注册成功");
						break;
				}
			}
        });
    }
  })
 
  </script>
