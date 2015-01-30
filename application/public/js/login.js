function _validate($field, pattern, msg, submit) {
    if (!pattern.test($field.val())) {
    	$field.addClass("invalid");
    	_showError($field, msg, submit);
    	return false;
    } else {
    	_hideError($field);
    	return true;
    }

  }

  function _showError($field, msg, submit) {
    if ($field.hasClass("invalid") || submit) {
    	$field.next().text(msg).removeClass("hide");
    	$field.parents(".form-group").addClass("has-error");
    }
  }
  
  function _hideError($field) {
	   	$field.next().addClass("hide");
		$field.parents(".form-group").removeClass("has-error");
  }

  function validateName($name, submit) {
    if ($.trim($name.val()) === "") {
	    $name.addClass("invalid");
	    _showError($name, "账号不能为空", submit);
	    return false;
    } else {
      return _validate($name, /^\w{6,12}$/, "只能是6~12位的数字字母下划线", submit);
    }
  }

  function validatePassword($password, submit) {
    if ($.trim($password.val()) === "") {
    	$password.addClass("invalid");
    	_showError($password, "密码不能为空", submit);
    	return false;
    } else {
    	return _validate($password, /^\w{6,12}$/, "只能是6~12位的数字字母下划线", submit);
    }
  }

  function validatePassword2($password2, $password, submit){
  	  if(validatePassword($password2)){
  		if($password2.val() !== $password.val()){
  			_showError($password2, "密码不一致", submit);
  			return false;
  		}else{
  			return true;
  		}
  	  }else{
  	  	return false;
  	  }
  }

  function validateEmail($email, submit) {
    if($.trim($email.val()) === ""){
    	$email.addClass("invalid");
    	_showError($email, "邮箱不能为空", submit);
    	return false;
    }else{
    	return _validate($email, /^[a-z\d]+(\.[a-z\d]+)*@([\da-z](-[\da-z])?)+(\.{1,2}[a-z]+)+$/, "邮箱格式不正确", submit);
    } 
  }