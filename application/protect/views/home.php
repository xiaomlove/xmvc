<style>
body{margin-bottom: 280px}
</style>
<div class="row page-header" style="margin-top: 20px;text-align: center">
	<div class="welcome"><h2>欢迎光临TinyHD</h2></div>
	<div class="userbox">
		<ul class="list-inline">
			<li>用户名:<span id="user-name"><?php echo framework\App::ins()->user->getName()?></span></li>
			<!--<li>上传量:<?php echo isset($userInfo['uploaded']) ? $this->getSize($userInfo['uploaded']) : 0?></li>
			<li>下载量:<?php echo isset($userInfo['downloaded']) ? $this->getSize($userInfo['downloaded']) : 0?></li>
			-->
			<li>登陆IP:<span id="ip"><?php echo framework\App::ins()->request->getClientIP()?></span></li>
		</ul>
		
	</div>
</div>

<!--<h3 style="text-align: center">Tiny Talk</h3>-->
<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<div class="row talk-wrap">
			<div class="col-md-9 talk-content" id="talk-content">
			<!--
				<div class="talk-join alert alert-info"><strong>xiaomiao</strong><small>加入了聊天室&nbsp;--&nbsp;<span>10:12:45</span></small></div>
				<div class="talk-item">
					<div class="talker-info">
						<div><img src="application/assets/images/avatar.jpg" class="img-responsive"/></div>
						<div class="name-level"><span>xiaomlove</span>(小学生)&nbsp;&nbsp;--&nbsp;&nbsp;<small>10:34:15</small></div>
					</div>
					<div class="alert ">
						哈哈，好开心！
					</div>
				</div>
				
				<div class="talk-item">
					<div class="talker-info">
						<div><img src="application/assets/images/avatar.jpg" class="img-responsive"/></div>
						<div class="name-level"><span>xiaomlove</span>(小学生)&nbsp;&nbsp;--&nbsp;&nbsp;<small>10:34:15</small></div>
					</div>
					<div class="alert ">
						上边煞逼一个！
					</div>
				</div>
				
				<div class="talk-join alert alert-info"><strong>xiaomlove</strong><small>加入了聊天室&nbsp;--&nbsp;<span>10:12:45</span></small></div>
				<div class="talk-item">
					<div class="talker-info">
						<div><img src="application/assets/images/avatar.jpg" class="img-responsive"/></div>
						<div class="name-level"><span>xiaomlove</span>(小学生)&nbsp;&nbsp;--&nbsp;&nbsp;<small>10:34:15</small></div>
					</div>
					<div class="alert ">
						有种来打我啊！
					</div>
				</div>
				<div class="talk-item">
					<div class="talker-info">
						<div><img src="application/assets/images/avatar.jpg" class="img-responsive"/></div>
						<div class="name-level"><span>xiaomlove</span>(小学生)&nbsp;&nbsp;--&nbsp;&nbsp;<small>10:34:15</small></div>
					</div>
					<div class="alert ">
						吐了！
					</div>
				</div>
				 -->
			</div>
			<div class="col-md-3 online-user">
				<div  class="online-user-head"><h4>在线用户<em>(<span id="user-count">0</span>)</em></h4></div>
				<div class="online-user-list">
					<ul class="list-unstyled" id="user-list">
					<!-- 
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
						<li><img src="application/assets/images/avatar.jpg" class="img-responsive"/>xiaomiao</li>
					 -->
					</ul>
				</div>
			</div>
			 
		</div>
		
		<div class="col-md-offset-2 col-md-6 submit-wrap">
			<div contenteditable="true" class="submit-form" id="submit-form"></div>
			<div class="btn-group">
			  <button type="button" class="btn btn-primary" id="launch">发射</button>
			  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			    <span class="caret"></span>
			    <span class="sr-only">Toggle Dropdown</span>
			  </button>
			  <ul class="dropdown-menu hotkey-launch" role="menu">
			  	<li class="active"><a href="javascript:;"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>不用快捷键</a></li>
			    <li><a href="javascript:;"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Enter&nbsp;键发射</a></li>
			    <li><a href="javascript:;"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>Ctrl+Enter&nbsp;发射</a></li>
			  </ul>
			</div>
<!-- 			<button class="btn btn-primary btn-sm submit-btn" id="close">关闭</button> -->
		</div>
		<div class="col-md-4 paopao-action">
			<img src="application/assets/images/paopao/35.png" class="img-responsive"/>
		</div>
	</div>
</div>
<div id="paopao-expression">
 <div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#tieba" aria-controls="tieba" role="tab" data-toggle="tab">百度贴吧</a></li>
    <li role="presentation"><a href="#qq" aria-controls="qq" role="tab" data-toggle="tab">QQ表情</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="tieba">
    	<img src="application/assets/images/paopao/1.png">
		<img src="application/assets/images/paopao/2.png">
		<img src="application/assets/images/paopao/3.png">
		<img src="application/assets/images/paopao/4.png">
		<img src="application/assets/images/paopao/5.png">
		<img src="application/assets/images/paopao/6.png">
		<img src="application/assets/images/paopao/7.png">
		<img src="application/assets/images/paopao/8.png">
		<img src="application/assets/images/paopao/9.png">
		<img src="application/assets/images/paopao/10.png">
		<img src="application/assets/images/paopao/11.png">
		<img src="application/assets/images/paopao/12.png">
		<img src="application/assets/images/paopao/13.png">
		<img src="application/assets/images/paopao/14.png">
		<img src="application/assets/images/paopao/15.png">
		<img src="application/assets/images/paopao/16.png">
		<img src="application/assets/images/paopao/17.png">
		<img src="application/assets/images/paopao/18.png">
		<img src="application/assets/images/paopao/19.png">
		<img src="application/assets/images/paopao/20.png">
		<img src="application/assets/images/paopao/21.png">
		<img src="application/assets/images/paopao/22.png">
		<img src="application/assets/images/paopao/23.png">
		<img src="application/assets/images/paopao/24.png">
		<img src="application/assets/images/paopao/25.png">
		<img src="application/assets/images/paopao/26.png">
		<img src="application/assets/images/paopao/27.png">
		<img src="application/assets/images/paopao/28.png">
		<img src="application/assets/images/paopao/29.png">
		<img src="application/assets/images/paopao/30.png">
		<img src="application/assets/images/paopao/31.png">
		<img src="application/assets/images/paopao/32.png">
		<img src="application/assets/images/paopao/33.png">
		<img src="application/assets/images/paopao/34.png">
		<img src="application/assets/images/paopao/35.png">
		<img src="application/assets/images/paopao/36.png">
		<img src="application/assets/images/paopao/37.png">
		<img src="application/assets/images/paopao/38.jpg">
		<img src="application/assets/images/paopao/39.png">
		<img src="application/assets/images/paopao/40.png">
		<img src="application/assets/images/paopao/41.png">
		<img src="application/assets/images/paopao/42.png">
		<img src="application/assets/images/paopao/43.png">
		<img src="application/assets/images/paopao/44.png">
		<img src="application/assets/images/paopao/45.png">
    </div>
    <div role="tabpanel" class="tab-pane" id="qq">
    	<?php 
    		for ($i = 1; $i <= 112; $i++)
    		{
    			echo '<img src="application/assets/images/qqbig/'.$i.'.gif">';
    		}
    	?>
    </div>
  </div>

</div>
<!-- 
	<img src="application/assets/images/paopao/1.png">
	<img src="application/assets/images/paopao/2.png">
	<img src="application/assets/images/paopao/3.png">
	<img src="application/assets/images/paopao/4.png">
	<img src="application/assets/images/paopao/5.png">
	<img src="application/assets/images/paopao/6.png">
	<img src="application/assets/images/paopao/7.png">
	<img src="application/assets/images/paopao/8.png">
	<img src="application/assets/images/paopao/9.png">
	<img src="application/assets/images/paopao/10.png">
	<img src="application/assets/images/paopao/11.png">
	<img src="application/assets/images/paopao/12.png">
	<img src="application/assets/images/paopao/13.png">
	<img src="application/assets/images/paopao/14.png">
	<img src="application/assets/images/paopao/15.png">
	<img src="application/assets/images/paopao/16.png">
	<img src="application/assets/images/paopao/17.png">
	<img src="application/assets/images/paopao/18.png">
	<img src="application/assets/images/paopao/19.png">
	<img src="application/assets/images/paopao/20.png">
	<img src="application/assets/images/paopao/21.png">
	<img src="application/assets/images/paopao/22.png">
	<img src="application/assets/images/paopao/23.png">
	<img src="application/assets/images/paopao/24.png">
	<img src="application/assets/images/paopao/25.png">
	<img src="application/assets/images/paopao/26.png">
	<img src="application/assets/images/paopao/27.png">
	<img src="application/assets/images/paopao/28.png">
	<img src="application/assets/images/paopao/29.png">
	<img src="application/assets/images/paopao/30.png">
	<img src="application/assets/images/paopao/31.png">
	<img src="application/assets/images/paopao/32.png">
	<img src="application/assets/images/paopao/33.png">
	<img src="application/assets/images/paopao/34.png">
	<img src="application/assets/images/paopao/35.png">
	<img src="application/assets/images/paopao/36.png">
	<img src="application/assets/images/paopao/37.png">
	<img src="application/assets/images/paopao/38.jpg">
	<img src="application/assets/images/paopao/39.png">
	<img src="application/assets/images/paopao/40.png">
	<img src="application/assets/images/paopao/41.png">
	<img src="application/assets/images/paopao/42.png">
	<img src="application/assets/images/paopao/43.png">
	<img src="application/assets/images/paopao/44.png">
	<img src="application/assets/images/paopao/45.png">
 -->
</div>

<input type="hidden" id="socket-url" value="<?php echo $this->createUrl('talk')?>">
<input type="hidden" id="user-id" value="<?php echo isset($userInfo['id']) ? $userInfo['id'] : ''?>">

<!-- 模板 -->
<div style="display: none" id="tpl-join">
	<div class="talk-join alert alert-info"><strong class="username">xiaomiao</strong>&nbsp;加入了聊天室&nbsp;--&nbsp;<small><span class="time">10:12:45</span></small></div>
</div>

<div style="display: none" id="tpl-leave">
	<div class="talk-join alert alert-info"><strong class="username">xiaomiao</strong>&nbsp;离开了聊天室&nbsp;--&nbsp;<small><span class="time">10:12:45</span></small></div>
</div>

<div style="display: none" id="tpl-connect-info">
	<div class="talk-join alert alert-info"><strong class="info">握手成功，正在登陆...</strong>&nbsp;--&nbsp;<small><span class="time">10:12:45</span></small></div>
</div>

<div style="display: none" id="tpl-message">
	<div class="talk-item">
		<div class="talker-info">
			<div><img src="application/assets/images/avatar.jpg" class="img-responsive"/></div>
			<div class="name-level"><span class="username">xiaomlove</span>&nbsp;&nbsp;--&nbsp;&nbsp;<small class="time">10:34:15</small></div>
		</div>
		<div class="alert message-content">
			哈哈，好开心！
		</div>
	</div>
</div>

<script>

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
		var username = $("#user-name").text();
		var userid = $("#user-id").val();
		var port = CONFIG.WebSocketPort;
		var host = "ws://"+CONFIG.WebSocketHost+":"+CONFIG.WebSocketPort;
// 		var host = "ws://127.0.0.1:2222";
		if (userid == ""){
			username = getCustomUsername();
			if(username === false || username.trim()== ""){
				return;
			}
			userid = new Date().getTime();
		}
		var $talkContent = $("#talk-content");
		var $join = $("#tpl-join");
		var $leave = $("#tpl-leave");
		var $connect = $("#tpl-connect-info");
		var $message = $("#tpl-message");
		var $userList = $("#user-list");
		var $userCount = $("#user-count");
		var $submitForm = $("#submit-form");
		var TYPE_HANDSHAKE = 0, TYPE_DISCONNECT = -1, TYPE_MESSAGE = 1, TYPE_JOIN = 2, TYPE_LOGIN = 3;

		if (window.WebSocket){
			var socket = new WebSocket(host);
		}else{
			$connect.find(".info").html("你的浏览器不支持WebSocket");
			$connect.find(".time").html((new Date()).toLocaleString());
			$talkContent.append($connect.html());
			return;
		}
		
		socket.onopen = function(e){
			//console.log("socket open.");
			$connect.find(".info").html("socket open，正在连接...");
			$connect.find(".time").html((new Date()).toLocaleString());
			$talkContent.append($connect.html());
		}

		socket.onmessage = function(e){
			var data = e.data;//json字符串而已！！！！！！！！！！！！！！
			data = JSON.parse(data);
			if (data.type == TYPE_HANDSHAKE){
				$connect.find(".info").html("握手成功，正在登陆...");
				$connect.find(".time").html(data.time);
				$talkContent.append($connect.html());
				//发送请求登陆信息
				//console.log("handshake success ask for join in"+username);
				var send = {type: TYPE_JOIN, userinfo: {userid: userid, username: username}};
				socket.send(JSON.stringify(send));
			}else if (data.type == TYPE_JOIN){
// 				//加入成功，返回在线用户列表
				//console.log("在线用户列表已返回："+data.msg);
				var userList = JSON.parse(data.msg);
				var HTML = [];
				for( var i in userList){
					HTML.push('<li data-id="'+userList[i].userid+'"><img src="application/assets/images/avatar.jpg" class="img-responsive"/>'+userList[i].username+'</li>');
				}
				$userList.append(HTML.join(""));
				$userCount.html(data.count);
			}else if(data.type == TYPE_MESSAGE){
				//console.log("正常消息："+data.msg);
				$message.find(".username").html(data.username);
				$message.find(".time").html(data.time);
				$message.find(".message-content").html(data.msg);
				append($message.html());
				
			}else if(data.type == TYPE_DISCONNECT){
				//console.log("用户退出："+data.msg.username);
				$leave.find(".username").html(data.msg.username);
				$leave.find(".time").html(data.time);
				append($leave.html());
				$userList.find("li[data-id="+data.msg.userid+"]").remove();
				$userCount.html(function(index, text){
					//console.log(text);
					return parseInt(text)-1;
				})
				
			}else if(data.type == TYPE_LOGIN){
				//用户登陆成功
				//console.log(data.msg);
				$join.find(".username").html(data.msg.username);
				$join.find(".time").html(data.time);
				append($join.html());
				var id = data.msg.userid;
				if (!$userList.find("li[data-id="+id+"]").length){
					var html = '<li data-id="'+data.msg.userid+'"><img src="application/assets/images/avatar.jpg" class="img-responsive"/>'+data.msg.username+'</li>';
					$userList.append(html);
					$userCount.html(function(index, text){
						return parseInt(text)+1;
					})
				}
			}
			
			
		}

		socket.onerror = function(e){
			console.log("error:"+e.data);
		}

		socket.onclose = function(e){
			//console.log("close:"+e.data);
			$connect.find(".info").html("已断开服务器连接...");
			$connect.find(".time").html((new Date()).toLocaleString());
			append($connect.html());
		}

		$("#launch").click(function(e){
			var html = $submitForm.html().trim();
			if (html == ""){
				alert("请输入内容");
				return;
			}
			var checkIt = '<img src="application/assets/images/paopao/"';
			if (checkIt.indexOf(html) == -1){
				html = html.replace(/<div><br><\/div>/gi, "");
				if (html.trim() == ""){
					alert("请输入内容");
					return;
				}
			}
			html = html.replace(/<div><br><\/div>/gi, "");
			//console.log(html);
			var send = JSON.stringify({type: TYPE_MESSAGE, msg: html, username: username});
			socket.send(send);
			$submitForm.empty();
			e.stopPropagation();
		});

		$("#close").click(function(e){
			socket.close();
		});

		function append(content){
			$talkContent.append(content);
			$talkContent.scrollTop(function(){
				return this.scrollHeight-$(this).height();
			});
		}

		function getCustomUsername(){
			var username = window.prompt("你没有登陆，请输入一个用于聊天时的名称");
			if (username == null){
				return false;
			}else if(username.trim() == ""){
				alert("无效！");
				getCustomUsername();
			}else{
				return username;
			}
		}

		var $hotkeyLaunch = $(".hotkey-launch");
		$hotkeyLaunch.children("li").on("click", function(){
			$(this).parent().children().removeClass("active");
			$(this).addClass("active");
		});

		$(window).on("keydown", function(e){
			if (document.activeElement.id == "submit-form" && e.keyCode == 13){
				var hotType = $hotkeyLaunch.children(".active").index();
				if (hotType == 0){
					return;
				}else if(hotType == 1){
					//Enter发射
					$("#launch").click();
				}else if(hotType == 2 && e.ctrlKey){
					$("#launch").click();
				}
			}
		});

		var $paopaoAction = $(".paopao-action"),$paopaoWrap = $("#paopao-expression");
		$paopaoAction.children(":first").on("mouseover", function(e){
			var offset = $(this).offset();
			var one = 32;
			$paopaoWrap.css({
				left: offset.left + one - 50,
				top: offset.top - 50,
			}).fadeIn();
		});

		$paopaoWrap.on("mouseleave", function(e){
			e.stopPropagation();
			$paopaoWrap.fadeOut();
		});

		$paopaoWrap.on("click", "img", function(){
 			var imgHtml = $(this).prop("outerHTML");
// 			var sel = window.getSelection(), range = document.createRange();
// 			var focusNode = sel.focusNode, focusOffset = sel.focusOffset;
// 			console.log(focusOffset);
			$submitForm.append(imgHtml).focus();
			//range.setStart(focusNode, focusOffset+1);
// 			console.log(focusNode.length);
		});

// 		$submitForm.on("click", function(){
// 			var sel = document.selection.createRange();
// 			sel.setStart()
// 		})
		
	})
</script>