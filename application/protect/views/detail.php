<div class="row torrent-detail">
	<?php if (!empty($torrent)):?>
      <h1 class="torrent-title"><?php echo $torrent['main_title']?></h1>
      <?php if(framework\App::ins()->user->hasFlash('upload_success')):?>
      <h1 id="upload-success-flash" class="torrent-title text-danger"><strong><?php echo framework\App::ins()->user->getFlash('upload_success')?></strong></h1>
      <?php endIf?>
      <table class="table table-bordered" id="detail-table">

        <tbody>
          <tr>
            <td>下载</td>
            <td><a href="download?id=<?php echo $torrent['id']?>"><?php echo $this->getTorrentName($torrent['name'])?></a></td>
          </tr>
          <tr>
            <td>副标题</td>
            <td><?php echo $torrent['slave_title']?></td>
          </tr>
          <tr>
            <td>基本信息</td>
            <td>
            	大小：<strong><?php echo $this->getSize($torrent['size'])?></strong>
            	类型：<strong><?php echo $this->getCategory('resource_type', $torrent['resource_type'])?></strong>
            	媒介：<strong><?php echo $this->getCategory('resource_medium', $torrent['resource_medium'])?></strong>
            	视频编码：<strong><?php echo $this->getCategory('video_encode', $torrent['video_encode'])?></strong>
            	音频编码：<strong><?php echo $this->getCategory('audio_encode', $torrent['audio_encode'])?></strong>
            	分辨率：<strong><?php echo $this->getCategory('resolution', $torrent['resolution'])?></strong>
            	制作组：<strong><?php echo $this->getCategory('team', $torrent['team'])?></strong>
            </td>
          </tr>
          
          <tr>
            <td>行为</td>
            <td><a href="javascript:;" class="text-danger">删除种子</a><a href="<?php echo framework\App::ins()->user->getId() == $torrent['user_id'] ? $this->createUrl('torrent/edit', array('id' => $torrent['id'])) : '#'?>" class="text-primary">编辑种子</a><a href="javascript:;" class="text-warning">举报种子</a></td>
          </tr>
          
          <?php if (!empty($torrent['douban_id'])):?>
          <tr>
            <td>豆瓣信息</td>
            <td id="douban_info" data-douban-id="<?php echo $torrent['douban_id']?>"></td>
          </tr>
          <?php endIf?>
          
          <tr>
            <td>简介</td>
            <td>
              <div id="introduce"><?php echo $torrent['introduce']?></div>
            </td>
          </tr>
          
          <tr>
            <td>种子信息</td>
            <td>
            	hash码：<span class="text-primary"><?php echo $torrent['info_hash']?></span>
            	<span><a href="javascript:;" id="view-filelist" data-show="false">[ <span class="view-action">查看</span>文件(<?php echo $torrent['file_count']?>) ]</a></span>
            	<?php echo $fileList?>
            </td>
          </tr>
          <tr>
            <td>热度表</td>
            <td>查看：<span class="text-primary"><?php echo $torrent['view_times']?>次</span>下载：<span class="text-primary"><?php echo $torrent['download_times']?>次</span>完成：<span class="text-primary"><?php echo $torrent['finish_times']?>次</span><span id="view-snatch"><a href="<?php echo $this->createUrl('torrent/snatch', array('id' => $torrent['id']))?>">[查看完成情况]</a></span></td>
          </tr>
          <tr>
            <td>同伴</td>
            <td>
            	<button class="btn btn-xs btn-info" id="partner"  data-haved="false">查看小伙伴们</button>做种者：<span class="text-primary"><em class="seeder-count"><?php echo $torrent['seeder_count']?></em>个</span>下载者：<span class="text-primary "><em class="leecher-count"><?php echo $torrent['leecher_count']?></em>个</span>
            	<div id="seeder-leecher-list" style="display: none">
            	
            	</div>
            </td>
          </tr>
          <tr>
            <td>感谢者</td>
            <td><button class="btn btn-success btn-xs">说谢谢</button><span class="text-primary">admin1</span><span class="text-primary">admin2</span><span class="text-primary">xiaomiao</span></td>
          </tr>
        </tbody>
      </table>
      
      <h3 class="torrent-title no-comment" id="comment-title">评论加载中...</h3>
      <div id="comment-list">
     <!--  
      	<div class="item">
        <h4 class="comment-head">#<span class="comment-floor">0</span><span class="text-primary comment-username">张三</span><span class="pull-right comment-add-time">2015-03-02</span></h4>
        <div class="clearfix">
          <div class="col-xs-2 avatar">
            <img src="application/assets/images/avatar.jpg" class="img-responsive"/>
          </div>
        
          <div class="col-xs-10 comment-box">
          	<div class="comment-content">
	          	<h6>来自1楼网友<span class="pull-right">1</span></h6>
	         	1楼的内容
	         	<h6 class="floor-reply">回复</h6>
         	</div>
         	2楼在此
         	
          </div>
        </div>
        <div class="clearfix comment-foot">
          <div class="col-xs-2 social">
            <span class="text-danger">私信</span><span class="text-primary">加好友</span>
          </div>
          <div class="col-xs-10 action">
            <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
            <span class="text-info">回复</span>
            <span class="text-danger">举报</span>            
          </div>
        </div>
      </div>
     
       -->
      </div>
   
   	<div id="submit-form">
      <h3 class="torrent-title" id="quick-comment">快速评论</h3>
      <form class="form-horizontal upload-form" role="form">    
        <div class="form-group">
          
          <div class="col-sm-offset-3 col-sm-6">
            <div class="form-control comment" contenteditable="true"></div>
            <textarea class="form-control" rows="4" name="comment" style="display: none"></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6 submit-btn">
            <div class="expression">
              <img src="application/assets/images/QQexpression/1.gif">
              <img src="application/assets/images/QQexpression/2.gif">
              <img src="application/assets/images/QQexpression/3.gif">
              <img src="application/assets/images/QQexpression/4.gif">
              <img src="application/assets/images/QQexpression/5.gif">
              <img src="application/assets/images/QQexpression/6.gif">
              <img src="application/assets/images/QQexpression/7.gif">
              <img src="application/assets/images/QQexpression/8.gif">
              <img src="application/assets/images/QQexpression/9.gif">
              <img src="application/assets/images/QQexpression/10.gif">
              <img src="application/assets/images/QQexpression/11.gif">
              <img src="application/assets/images/QQexpression/12.gif">
              <img src="application/assets/images/QQexpression/13.gif">
              <img src="application/assets/images/QQexpression/14.gif">
              <img src="application/assets/images/QQexpression/15.gif">
              <img src="application/assets/images/QQexpression/16.gif">
            </div>
            <button type="button" class="btn btn-primary submit">添加</button>
            
          </div>
        </div>
      </form>
     </div>
      <?php endIf?>
    </div>
    <input type="hidden" id="torrentId" value="<?php echo $torrent['id']?>">
    <input type="hidden" id="baseUrl" value="<?php echo framework\App::ins()->request->getBaseUrl()?>">
    <input type="hidden" id="username" value="<?php echo framework\App::ins()->user->getName()?>">
    <input type="hidden" id="getSeederLeecherUrl" value="<?php echo $this->createUrl('torrent/getSeederLeecher')?>">
    <input type="hidden" id="user-profile-baseurl" value="">
    <div id="tpl" style="display:none;">
   
    <div class="item" data-id="0">
        <h4 class="comment-head">#<span class="comment-floor">0</span><span class="text-primary comment-username">张三</span><small class="pull-right comment-add-time">2015-03-02</small></h4>
        <div class="clearfix">
          <div class="col-xs-2 avatar">
            <img src="application/assets/images/avatar.jpg" class="img-responsive"/>
          </div>
          <div class="col-xs-10 comment-box">评论内容</div>
        </div>
        <div class="clearfix comment-foot">
          <div class="col-xs-2 social">
            <span class="text-danger">私信</span><span class="text-primary">加好友</span>
          </div>
          <div class="col-xs-10 action">
            <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
            <span class="text-info reply">回复</span>
            <span class="text-danger">举报</span>            
          </div>
        </div>
      </div>
    </div>
    
<div id="seeder-leecher-list-tpl" style="display: none;">
<table class="table table-hover">
	<caption>做种者</caption>
	<thead>
		<tr>
			<th>用户名</th>
			<th>可连接</th>
			<th>上传量</th>
			<th>即时速度</th>
			<th>下载量</th>
			<th>即时速度</th>
			<th>分享率</th>
			<th>完成</th>
			<th>连接时间</th>
			<th>最近汇报</th>
			<th>客户端</th>
		</tr>
	</thead>
	<tbody>
		
	</tbody>
</table>
</div>
 <script src="<?php echo framework\App::ins()->request->getBaseUrl()?>application/assets/lib/ueditor/ueditor.parse.min.js"></script>
 <script>
 	uParse('#introduce', {
	    rootPath: './'
	});
	//添加图片表情
	$("body").on("click", ".expression img", function(e){
		var $comment = $(this).parents("form").find(".comment"), sel = window.getSelection(), range = document.createRange();
		var focusNode = sel.focusNode, baseUrl = $("#baseUrl").val();
		if ($(focusNode).hasClass("comment") || $(focusNode).parents(".comment").length){
			
			range.setStart(focusNode, sel.focusOffset);
			var node = document.createElement("img");
			node.src = this.src.replace(baseUrl, "");
			range.insertNode(node);
		}
	});
	//添加回复和评论
	var maxFloor = 1;
	$("body").on("click", ".submit", function(e){
		var comment = $(this).parents("form").find(".comment").html();
		var $submit = $(this);
		var trim = $.trim(comment);
		var replace = $.trim(trim.replace(/<br>|&nbsp;|<div>|<\/div>/gi, ""));
		if (replace === ""){
			alert("请输入内容！");
			return;
		}
		var $form = $(this).parents("form");
		var floorReply = false;
		if($form.attr("data-parent")){//不是最下边直接回复框
			floorReply = true;
			if ($form.parent().hasClass("item")){
				//回复末楼
				var $oldComment = $form.parent().find(".comment-box");
				$clone = $oldComment.clone();//克隆一新的，不能在原基础上添加
				var addText = '<h6 class="floor-title">'+$form.parents(".item").find(".comment-username").text()+'&nbsp;&nbsp的评论<span class="pull-right floor-count">';
				if ($clone.children(":first").hasClass("comment-content")){
					$floorCount = $form.parent().find(".comment-box").children().children(".floor-title").children(".floor-count");
					
					addText += parseInt($floorCount.text())+1+"</span></h6>";
					$clone.children(":first").after(addText);
					
				}else{
					addText += "1</span></h6>";
					$clone.prepend(addText);
				}
				$clone.append("<h6 class=\"floor-reply\"><span class=\"reply\">回复</span></h6>");
				$clone.wrapInner("<div class=\"comment-content\"></div>");
				
				comment = $clone.html()+comment;
				
			}else if($form.parent().hasClass("comment-content")){
				//楼中楼回复
				$comment = $form.parent().clone();
				$comment.find("form").remove().end().children(".floor-reply").children(".reply").removeClass("submit-show");
				comment = $comment.prop("outerHTML") + comment;
				
			}
			
		}
		var torrentId = $("#torrentId").val();
		var $total = $("#comment-total");
		if ($total.length){
			maxFloor = parseInt($total.val())+1;
		}
		$.ajax({
			url: "comment/add",
			data: "torrentId="+torrentId+"&comment="+encodeURIComponent(comment)+"&floor="+maxFloor,
			type: "POST",
			dataType: "json",
			beforeSend: function(){$submit.text("添加中...").attr("disabled", "disabled")},
			success: function(data){
				if (data.code === 1){
					$submit.text("添加").removeAttr("disabled");
					$("#submit-form").find(".comment").empty();
					var $new = $("#tpl").children().clone();
					$new.find(".comment-floor").html(maxFloor);
				
					$new.find(".comment-username").text($("#username").val());
					$new.find(".comment-add-time").text(new Date().getTime());
					$new.find(".comment-box").html(comment);
					if ($("#comment-item").length){
						$("#comment-item").append($new);
					}else{
						$("#comment-list").append($new);
					}
					if ($total.length){
						$total.val(maxFloor);
					}else{
						maxFloor++;
					}
					if (floorReply){
						$form.remove();
					}
					var $commentTitle = $("#comment-title");
					if ($commentTitle.hasClass("no-comment")) {
						$commentTitle.text("用户评论").removeClass("no-comment");
					}
				}else{
					$submit.text(data.msg).removeAttr("disabled");
				}
			}
		})
		
	});

	//页面加载完成请求首页评论
	$(document).ready(function(){
		var $commentTitle = $("#comment-title");
		$.ajax({
			url: "comment/list",
			type: "GET",
			dataType: "json",
			data: "torrentId="+$("#torrentId").val(),
			success: function(data){
				if (data.code === 1){
					$commentTitle.text("用户评论").removeClass("no-comment");
					$("#comment-list").html(data.msg);
				}else{
					$commentTitle.text(data.msg);
				}
			}
		})
	});
	//分页获取评论
	var $commentList = $("#comment-list");
		$commentList.on("click", ".comment-list-nav a", function(e){
			var $click = $(this).parent();
			var total = $("#comment-page").val();
			if ($click.hasClass("disabled")){
				return;
			}
			var $page = $(this).parents(".pagination").children(".active:first");
			var page = $page.find("span:first").text();
			if ($(this).hasClass("prev")){
				page = parseInt(page)-1;
			}else if($(this).hasClass("next")){
				page = parseInt(page)+1;
			}else{
				page = parseInt($(this).children("span").text());
			} 
			 $.ajax({
					url: "comment/list",
					type: "GET",
					dataType: "json",
					data: "torrentId="+$("#torrentId").val()+"&page="+page,
					success: function(data){
						if (data.code === 1){
							$commentList.html(data.msg);
							var $nav = $commentList.find(".comment-list-nav");
							$nav.each(function(index, elem){
								$(this).find("li").removeClass("active disabled");
								$(this).find("li").eq(page).addClass("active");
								if (page == total){
									$(this).find("li").eq(page+1).attr("class", "disabled");
								}else if(page == "1"){
									$(this).find("li").eq(page-1).attr("class", "disabled");
								}
							});
							
						}
					}
			})
	 });
	//展示楼中楼回复框
	$commentList.on("click", ".item .reply", function(e){
		
		var $this = $(this);
		if ($this.hasClass("submit-show")){
			return;
		}
		
		var $form = $("#submit-form").find("form:first").clone();
		$form.attr("data-parent", "true").find(".comment").empty();
		var $submit = $form.find(".submit");
		var $cancle = $submit.clone();
		$cancle.text("取消").on("click", function(e){
			$this.removeClass("submit-show");
			$form.remove();
		});
		$submit.after($cancle);
		$commentList.find("form").remove();
		$commentList.find(".reply").removeClass("submit-show");
		var $commentFoot = $this.parents(".comment-foot"), $footReply = $this.parents(".floor-reply");
		if ($commentFoot.length){
			$commentFoot.after($form);
		}else if($footReply.length){
			$footReply.after($form);
		}else{
			return;
		}
		$this.addClass("submit-show");
	});
	//鼠标移上去显示楼中楼回复按钮
	$commentList.on("mouseenter mouseleave", ".comment-content", function(e){
		e.stopPropagation();
		var $reply = $(this).children(".floor-reply").find(".reply");
		if(e.type === "mouseenter"){
// 			$commentList.find(".reply").css("visibility", "hidden");
			$reply.css("visibility", "visible");
		}else{
			$reply.css("visibility", "hidden");
		}
		
	});


	//查看小伙伴
	var $checkPartner = $("#partner");
	$checkPartner.on("click", function(e){
		var $parent = $(this).parent();
		var $wrap = $("#seeder-leecher-list");
		if (!$(this).hasClass("showed")){
			//收起状态，要展开
			console.log('ss');
			if ($(this).attr("data-haved") == "false"){
				//还没有数据
				
				var seederCount = $parent.find(".seeder-count").text();
				var leecherCount = $parent.find(".leecher-count").text();
				if (seederCount == 0 && leecherCount == 0){
					return;
				}
				$.ajax({
					url: $("#getSeederLeecherUrl").val(),
					type: "GET",
					dataType: "json",
					data: "seederCount="+seederCount+"&leecherCount="+leecherCount+"&id="+$("#torrentId").val(),
					beforeSend: function(){$checkPartner.text("正在呼唤小伙伴们...").attr("disabled", true)},
					success: function(data){
						console.log(data);
						if (data.code == 1){
							if (data.msg.seeder){
								var seederTable = renderTable("做种者", data.msg.seeder);
								$wrap.append(seederTable);					
							}
							if(data.msg.leecher){
								var leecherTable = renderTable("下载者", data.msg.leecher);
								$wrap.append(leecherTable);
							}
							$checkPartner.attr("data-haved", "true");
							$wrap.slideDown();
							$checkPartner.text("隐藏小伙伴们").removeAttr("disabled").addClass("showed");
						}else{
							$checkPartner.text(data.msg);
							console.log(data);
						}
						if (data.updateSeederCount){
							console.log("updateSeederCount");
						}
						if (data.updateLeecherCount){
							console.log("updateLeecherCount");
						}					
					}
				})
			}else{
				//已有数据
				$wrap.slideDown();
				$checkPartner.text("隐藏小伙伴们").addClass("showed");
			}
		}else{
			$wrap.slideUp();
			$checkPartner.text("查看小伙伴们").removeClass("showed");
		}
	});

	function renderTable(caption, data){
		$tpl = $("#seeder-leecher-list-tpl").children();
		$tpl.find("caption").text(caption+"("+data.count+")");
		var TRHTML = [];
		$.each(data.data, function(index, item){
			TRHTML.push("<tr>");
			TRHTML.push("<td>"+item.username+"</td>");
			TRHTML.push("<td>"+(item.connectable == 1 ? "是" : "<span style=\"color: red;font-weight: bold\">否</span>")+"</td>");
			TRHTML.push("<td>"+item.uploaded_converted+"</td>");
			TRHTML.push("<td>"+item.upload_speed+"</td>");
			TRHTML.push("<td>"+item.downloaded_converted+"</td>");
			TRHTML.push("<td>"+item.download_speed+"</td>");
			if (item.downloaded == 0){
				TRHTML.push("<td>---</td>");
			}else{
				var ratio = (item.uploaded/item.downloaded).toFixed(3);
				if (ratio > 0.1){
					TRHTML.push("<td>"+ratio+"</td>");
				}else{
					TRHTML.push("<td><span style=\"color: red;font-weight: bold\">"+ratio+"</span></td>");
				}
			}			
			TRHTML.push("<td>"+(item.is_seeder == 1 ? 100 : (item.downloaded/item.torrent_size)*100).toFixed(2)+"%</td>");//完成百分比
			TRHTML.push("<td>"+(item.connect_time ? item.connect_time : 0)+"</td>");
			TRHTML.push("<td>"+item.this_report_time+"</td>");
			TRHTML.push("<td>"+item.agent+"</td>");
			TRHTML.push("</tr>");
		});
		$tpl.find("tbody").append(TRHTML.join(""));
		var toReturn = $tpl.prop("outerHTML");
		$tpl.find("tbody").empty();
		return toReturn;
	}

	//获取豆瓣信息
	var $doubanTd = $("#douban_info");
	if ($doubanTd.length) {
		var url = 'https://api.douban.com/v2/movie/subject/' + $doubanTd.attr('data-douban-id');
		$.ajax({
			url: url + '?callback=?',
			dataType: 'jsonp',
		}).done(function(result) {
// 			console.log(result);
			var HTML = createInfoHtml(result);
			$doubanTd.html(HTML);
		}).error(function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		})
	}

	function createInfoHtml(result) {
		const C = '◎';
		const BR = '<br/>';
		const SP = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
		var HTML = '<p class="douban-basic">' + C + '片' + SP + '名：' + result.original_title + BR;
		HTML += C + '译' + SP + '名：' + result.title + BR;
		HTML += C + '又' + SP + '名：' + arrToString(result.aka, null, '/') + BR;
		HTML += C + '年' + SP + '代：' + result.year + BR;
		HTML += C + '国家地区：' + arrToString(result.countries, null, '/') + BR;
		HTML += C + '类' + SP + '型：' + arrToString(result.genres, null, '/') + BR;
		if ('languages' in result) {
			HTML += C + '语' + SP + '言：' + arrToString(result.languages, null, '/') + BR;
		}
		if ('durations' in result) {
			HTML += C + '时' + SP + '长：' + arrToString(result.durations, null, '/') + BR;
		}
		HTML += C + '豆瓣评分：' + result.rating.average + '/10(' + result.ratings_count + ' votes)' + BR;
		HTML += C + '豆瓣链接：<a href="' + result.alt + '">' + result.alt + '</a>' + BR;
		
		HTML += C + '导' + SP + '演：' + arrToString(result.directors, 'name', '/') + BR;

		HTML += C + '演' + SP + '员：' + result.casts.shift().name + BR;
		const len = result.casts.length;
		for (var i = 1; i < len; i++) {
			HTML += SP + SP + SP + result.casts[i].name + BR;
		}
		HTML +='</p>' + BR;	
		HTML += C + '简' + SP + '介：' + BR + BR;
		HTML += '<p>' + SP + result.summary + '</p>';
		return HTML;
	}

	function arrToString(arr, field, d) {
		var doSub = false;
		if (typeof d === 'undefined') {
			d = '';
		} else {
			doSub = true;
		}
		var STR = '';
		const len = arr.length;
		for (var i = 0;i < len; i++) {
			if (field) {
				STR += arr[i][field] + d;
			} else {
				STR += arr[i] + d;
			}
		}
		if (doSub) {
			STR = STR.substring(0, STR.lastIndexOf(d));
		}
		return STR;
	}

	//查看文件列表
	var $viewFileListBtn = $('#view-filelist');
	var $fileListTable = $('.file-list-table');
	$viewFileListBtn.click(function(e) {
		if ($viewFileListBtn.attr('data-show') == 'false') {
			$fileListTable.slideDown(function() {
				$viewFileListBtn.attr('data-show', 'true').find('.view-action').text('隐藏');
			});
		} else {
			$fileListTable.slideUp(function() {
				$viewFileListBtn.attr('data-show', 'false').find('.view-action').text('查看');
			});
		}
	});
	
 </script>