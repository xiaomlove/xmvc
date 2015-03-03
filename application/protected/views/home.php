
 <h1 class="page-header">欢迎光临TinyHD</h1>
<div class="row">
	<div class="col-md-offset-3 col-md-6">
		<table class="table table-bordered table-hover" id="profile">
			<caption>个人信息<a class="btn btn-danger btn-xs pull-right" data-toggle="modal" data-target=".bs-example-modal-sm">退出</a></caption>
			<tr>
				<td>用户名：</td>
				<td><?php echo App::ins()->user->getName()?></td>
			</tr>
			<tr>
				<td>登陆IP：</td>
				<td id="ip"><?php echo App::ins()->request->getClientIP()?></td>
			</tr>
			<tr>
				<td>上传量：</td>
				<td><?php echo isset($userInfo['uploaded']) ? $userInfo['uploaded'] : 0?></td>
			</tr>
			<tr>
				<td>下载量：</td>
				<td><?php echo isset($userInfo['downloaded']) ? $userInfo['downloaded'] : 0?></td>
			</tr>
				
		</table>
	</div>
</div>

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