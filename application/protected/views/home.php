<div class="row page-header" style="margin-top: 20px">
	<div class="welcome"><h2>欢迎光临TinyHD</h2></div>
	<div class="userbox">
		<ul class="list-inline">
			<li>用户名:<span id="user-name"><?php echo App::ins()->user->getName()?></span></li>
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
			<div class="col-md-9 talk-content" id="talk-content">
			<!--
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
				 -->
			</div>
			<div class="col-md-3 online-user">
				<div  class="online-user-head"><h4>在线用户<em id="user-count">(12)</em></h4></div>
				<div class="online-user-list">
					<ul class="list-unstyled" id="user-list">
					<!-- 
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
					 -->
					</ul>
				</div>
			</div>
			 
		</div>
		
		<div class="col-md-offset-2 col-md-6 submit-wrap">
			<div contenteditable="true" class="submit-form" id="submit-form"></div>
			<button class="btn btn-primary btn-sm submit-btn" id="launch">发射</button>
			<button class="btn btn-primary btn-sm submit-btn" id="close">关闭</button>
		</div>
	</div>
</div>

<div id="checkout-logout-modal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      
      <div class="modal-body">
        	<strong>确定退出吗？</strong>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" id="logout">确定</button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="socket-url" value="<?php echo $this->createUrl('talk')?>">
<input type="hidden" id="user-id" value="<?php echo $userInfo['id']?>">
<script type="application/tpl" data-type="alert">
<div class="talk-join alert alert-info"><strong class="username">xiaomiao</strong><small>加入了聊天室&nbsp;--&nbsp;<span class="login-time">10:12:45</span></small></div>
</script>
<script type="application/tpl" data-type="message">
<div class="talk-item">
	<div class="talker-info">
		<div><img src="application/public/images/avatar.jpg" class="img-responsive"/></div>
		<div class="name-level"><span class="username">xiaomlove</span>&nbsp;&nbsp;--&nbsp;&nbsp;<small class="message-time">10:34:15</small></div>
	</div>
	<div class="alert message-content">
		哈哈，好开心！
	</div>
</div>
</script>
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
		var port = 8888;
		var host = "ws://"+location.host+":"+port;
// 		var host = "ws://127.0.0.1:2222";
		var socket = new WebSocket(host);
		var username = $("#user-name").text();
		var userid = $("#user-id").val();
		var $talkContent = $("#talk-content");
		var $alert = $("script[data-type=alert]");
		var $message = $("script[data-type=message]");
		var TYPE_HANDSHAKE = 0, TYPE_DISCONNECT = -1, TYPE_MESSAGE = 1, TYPE_JOIN = 2, TYPE_LOGIN = 3;
		
		socket.onopen = function(e){
			console.log("socket open.");
			$alert.children(".talk-join").html("socket open，正在连接...");
			console.log($alert.html());return;
			$talkContent.append($alert.html());
		}

		socket.onmessage = function(e){
			var data = e.data;
			console.log("receive message:"+data);
			if (data.type == TYPE_HANDSHAKE){
				$alert.children(".talk-join").text("握手成功，正在登陆...");
				$talkContent.append($alert.html());
				//发送请求登陆信息
				var send = {type: TYPE_JOIN, userinfo: {userid: userid, username: username}};
				socket.send(JSON_stringify(send));
			}else if (data.type == TYPE_JOIN){
				//登陆成功，返回在线用户列表
				console.log("在线用户列表已返回");
			}
			
			
		}

		socket.onerror = function(e){
			console.log("error:"+e.data);
		}

		socket.onclose = function(e){
			console.log("close:"+e.data);
		}

		$("#launch").click(function(e){
			var text = $("#submit-form").text().trim();
			if (text == ""){
				alert("请输入内容");
				return;
			};
			var send = JSON.stringify({type: TYPE_MESSAGE, msg: text});
			socket.send(send);
			
		});

		$("#close").click(function(e){
			socket.close();
		})
		
	})
</script>