<div class="row page-header" style="margin-top: 20px">
	<div class="welcome"><h2>欢迎光临TinyHD</h2></div>
	<div class="userbox">
		<ul class="list-inline">
			<li>用户名:<?php echo App::ins()->user->getName()?></li>
			<li>上传量:<?php echo $this->getSize($userInfo['uploaded'])?></li>
			<li>下载量:<?php echo $this->getSize($userInfo['downloaded'])?></li>
			<li>登陆IP:<span id="ip"><?php echo App::ins()->request->getClientIP()?></span></li>
		</ul>
		
	</div>
	<a class="btn btn-danger btn-xs pull-right" data-toggle="modal" data-target=".bs-example-modal-sm" style="margin-top: 30px">退出</a>
</div>

<h3 style="text-align: center">Tiny Talk</h3>
<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<div class="row talk-wrap">
			<div class="col-md-9 talk-content">
				<div class="talk-join alert alert-info"><strong>xiaomiao</strong><small>加入了聊天室&nbsp;--&nbsp;<span>10:12:45</span></small></div>
				<div class="talk-item">
					<div class="talker-info">
						<div><img src="application/public/images/avatar.jpg" class="img-responsive"/></div>
						<div class="name-level"><span>xiaomlove</span>(小学生)&nbsp;&nbsp;--&nbsp;&nbsp;<small>10:34:15</small></div>
					</div>
					<div class="alert ">
						哈哈，好开心！
					</div>
				</div>
				<div class="talk-item">
					<div class="talker-info">
						<div><img src="application/public/images/avatar.jpg" class="img-responsive"/></div>
						<div class="name-level"><span>xiaomlove</span>(小学生)&nbsp;&nbsp;--&nbsp;&nbsp;<small>10:34:15</small></div>
					</div>
					<div class="alert ">
						上边煞逼一个！
					</div>
				</div>
				<div class="talk-join alert alert-info"><strong>xiaomlove</strong><small>加入了聊天室&nbsp;--&nbsp;<span>10:12:45</span></small></div>
				<div class="talk-item">
					<div class="talker-info">
						<div><img src="application/public/images/avatar.jpg" class="img-responsive"/></div>
						<div class="name-level"><span>xiaomlove</span>(小学生)&nbsp;&nbsp;--&nbsp;&nbsp;<small>10:34:15</small></div>
					</div>
					<div class="alert ">
						有种来打我啊！
					</div>
				</div>
				<div class="talk-item">
					<div class="talker-info">
						<div><img src="application/public/images/avatar.jpg" class="img-responsive"/></div>
						<div class="name-level"><span>xiaomlove</span>(小学生)&nbsp;&nbsp;--&nbsp;&nbsp;<small>10:34:15</small></div>
					</div>
					<div class="alert ">
						吐了！
					</div>
				</div>
			</div>
			<div class="col-md-3 online-user">
				<div  class="online-user-head"><h4>在线用户<em>(12)</em></h4></div>
				<div class="online-user-list">
					<ul class="list-unstyled">
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/public/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="col-md-offset-2 col-md-6 submit-wrap">
			<div contenteditable="true" class="submit-form"></div>
			<button class="btn btn-primary btn-sm submit-btn">发射</button>
		</div>
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
		//获取IP信息
		var $ip = $("#ip");
		if($.trim(ip) !== "unknown"){
			$.ajax({
				url: "getipinfo?ip="+$.trim($ip.text()),
				type: "GET",
				async: true,
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

		//开启websocker聊天
		
	})
</script>