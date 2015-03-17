<div class="row page-header">
	<div class="welcome"><h2>欢迎光临TinyHD</h2></div>
	<div class="userbox">
		<ul class="list-inline text-success">
			<li>用户名:<?php echo App::ins()->user->getName()?></li>
			<li>上传量:<?php echo $this->getSize($userInfo['uploaded'])?></li>
			<li>下载量:<?php echo $this->getSize($userInfo['downloaded'])?></li>
			<li>登陆IP:<span id="ip"><?php echo App::ins()->request->getClientIP()?></span></li>
		</ul>
		
	</div>
	<a class="btn btn-danger btn-xs pull-right" data-toggle="modal" data-target=".bs-example-modal-sm" style="margin-top: 30px">退出</a>
</div>

<h3 style="text-align: center">聊天室</h3>

<div id="checkout-logout-modal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      
      <div class="modal-body">
        	<string>确定退出吗？</strong>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" id="logout">确定</button>
      </div>
    </div>
  </div>
</div>

<script>
	var $logout = $("#logout");
	$logout.on("click", function(e){
		$.ajax({
			url: "logout",
			dataType: "json",
			type: "POST",
			beforeSend: function(){$logout.attr("disabled", "disabled").text("退出中...")},
			success: function(data){
				if(data.code === 1){
					window.location.href="login.html";
				}else{
					$logout.text(data.msg);
				}
			}
		})
	});

	$(document).ready(function(){
		var $ip = $("#ip");
		if($.trim(ip) !== "unknown"){
			$.ajax({
				url: "getipinfo?ip="+$.trim($ip.text()),
				type: "GET",
				dataType: "json",
				success: function(data){
// 					console.log(data);return;
					if(data.code === 0){
						var result = data.data;
						var info = result.country + "&nbsp;"
							+ result.area + "&nbsp;"
							+ result.region + "&nbsp;"
							+ result.city + "&nbsp;"
							+ result.isp;
						$ip.html(function(index, html){
							return html + "(" + info + ")";
						})
					}else if(data.code === 1){
						console.log('公共接口返回失败');
					}else if(data.code === -1){
						console.log('请求公共接口失败');
					}
				}
			})
		}
	})
</script>