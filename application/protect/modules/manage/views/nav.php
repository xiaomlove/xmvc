<div class="row">
	<div class="col-md-12">
		<h3 class="page-header">编辑主菜单<span class="btn btn-info pull-right">保存</span></h3>
	</div>
</div>

<div class="row">
	<div class="col-md-3">
		<form id="form">
          <div class="form-group">
            <label for="name">文字</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="菜单项上显示文字">
            <span id="" class="help-block hidden">A block of help text that breaks onto a new line and may extend beyond one line.</span>
          </div>
          
          <div class="form-group">
            <label for="url">链接</label>
            <input type="text" class="form-control" id="url" name="url" placeholder="点击菜单后跳转的链接">
            <span id="" class="help-block hidden">A block of help text that breaks onto a new line and may extend beyond one line.</span>
          </div>
          
          <div class="form-group">
            <label for="target">打开方式</label>
            <select name="target" id="target" class="form-control">
                <option value="_self">_self（当前页面）</option>
                <option value="_blank">_blank（新窗口或新标签）</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="class-name">class类名(可选)</label>
            <input type="text" class="form-control" id="class-name" name="class_name" placeholder="菜单项的类名">
          </div>
          
           <div class="form-group text-center">
               <a class="btn btn-primary" id="submit">添加菜单项</a>
           </div>
        </form>
	</div>
	<div class="col-md-9">
	   <h4>菜单结构。按住一项可上下移动改变顺序，左右移动改变层级。点击一项右边箭头修改。</h4>
	   <div id="nav" class="nav-structure">
	       <ul class="list-unstyled">
	           <li class="nav-item">
	               <div>首页<span class="glyphicon glyphicon-triangle-bottom pull-right" aria-hidden="true"></span></div>
	               <div>
	                   <form id="" class="structure-form">
                          <div class="form-group-sm">
                            <label for="name">文字</label>
                            <input type="text" class="form-control" id="" name="name" placeholder="菜单项上显示文字">
                            <span id="" class="help-block hidden">A block of help text that breaks onto a new line and may extend beyond one line.</span>
                          </div>
                          
                          <div class="form-group-sm">
                            <label for="url">链接</label>
                            <input type="text" class="form-control" id="" name="url" placeholder="点击菜单后跳转的链接">
                            <span id="" class="help-block hidden">A block of help text that breaks onto a new line and may extend beyond one line.</span>
                          </div>
                          
                          <div class="form-group-sm">
                            <label for="target">打开方式</label>
                            <select name="target" id="" class="form-control">
                                <option value="_self">_self（当前页面）</option>
                                <option value="_blank">_blank（新窗口或新标签）</option>
                            </select>
                          </div>
                          
                          <div class="form-group-sm">
                            <label for="class-name">class类名(可选)</label>
                            <input type="text" class="form-control" id="" name="class_name" placeholder="菜单项的类名">
                          </div>
                        </form>
	               </div>
	           </li>
	           <li class="nav-item">论坛</li>
	           <li>
	               <span class="nav-item">种子</span>
	               <ul class="list-unstyled">
	                   <li class="nav-item">电影</li>
	                   <li class="nav-item">综艺</li>
	                   <li class="nav-item">电视剧</li>
	               </ul>
	           </li>
	           <li class="nav-item">发布</li>
	       </ul>
	   </div>
	</div>
</div>
<script>
var $name = $('#name'), $url = $('#url'), $target = $('#target'), $className = $('#class-name'), $submit = $('#submit');
var $form = $('#form');
var submitUrl = '<?php echo $this->createUrl('manage/nav/add')?>';
$submit.click(function(e) {
    if (checkName() && checkUrl()) {
        var name = $name.val().trim();
        var url = $url.val().trim();
        var className = $className.val().trim();
        var target = $target.val().trim();
        var data = 'name=' + encodeURIComponent(name) + '&url=' + encodeURIComponent(url) + '&class_name=' + encodeURIComponent(className) + '&target=' + target;
        $.ajax({
            url: submitUrl,
            type: 'post',
            dataType: 'json',
            timeout: 8000,
            data: data,
            beforeSend: function() {$submit.text('添加中...').addClass('disabled');},
        }).done(function(result) {
            console.log(result);
            reset();
            
        }).error(function(xhr, errorText) {
            console.log(errorText);
        })
    }	
});

function checkName() {
	if ($name.val().trim() === '') {
		showError('名字必须填写', $name);
		return false;
	} else {
		hideError($name);
		return true;
	}
}

function checkUrl() {
	if ($url.val().trim() === '') {
		showError('链接地址必须填写', $url);
		return false;
	} else {
		hideError($url);
		return true;
	}
}

function showError(msg, $obj) {
	$obj.closest('.form-group').addClass('has-error');
	$obj.next().text(msg).removeClass('hidden');
	$obj.focus();
}

function hideError($obj) {
	$obj.closest('.form-group').removeClass('has-error');
	$obj.next().addClass('hidden');
}

function reset() {
	$form.get(0).reset();
	$submit.text('添加菜单项').removeClass('disabled');
}
</script>
