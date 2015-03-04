<div class="row torrent-detail">
	<?php if (!empty($torrent)):?>
      <h1 class="torrent-title"><?php echo $torrent['main_title']?></h1>
      <?php if(App::ins()->user->hasFlash('upload_success')):?>
      <h1 id="upload-success-flash" class="torrent-title text-danger"><strong><?php echo App::ins()->user->getFlash('upload_success')?></strong></h1>
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
            <td>大小：<span class="text-success"><?php echo $this->getSize($torrent['size'])?></span>类型：<span class="text-success">Movie</span></td>
          </tr>
          <tr>
            <td>行为</td>
            <td><a href="#" class="text-danger">删除种子</a><a href="#" class="text-primary">编辑种子</a><a href="#" class="text-warning">举报种子</a></td>
          </tr>
          <tr>
            <td>简介</td>
            <td>
              <div id="introduce"><?php echo $torrent['introduce']?></div>
            </td>
          </tr>
          <tr>
            <td>种子信息</td>
            <td>hash码：<span class="text-primary"><?php echo $torrent['info_hash']?></span></td>
          </tr>
          <tr>
            <td>热度表</td>
            <td>查看：<span class="text-primary"><?php echo $torrent['view_times']?>次</span>下载：<span class="text-primary"><?php echo $torrent['download_times']?>次</span>完成：<span class="text-primary"><?php echo $torrent['finish_times']?>次</span></td>
          </tr>
          <tr>
            <td>同伴</td>
            <td><button class="btn btn-xs btn-info">查看小伙伴们</button>做种者：<span class="text-primary"><?php echo $torrent['seeder_count']?>个</span>下载者：<span class="text-primary"><?php echo $torrent['leecher_count']?>个</span></td>
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
            <img src="application/public/images/avatar.jpg" class="img-responsive"/>
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
              <img src="application/public/images/QQexpression/1.gif">
              <img src="application/public/images/QQexpression/2.gif">
              <img src="application/public/images/QQexpression/3.gif">
              <img src="application/public/images/QQexpression/4.gif">
              <img src="application/public/images/QQexpression/5.gif">
              <img src="application/public/images/QQexpression/6.gif">
              <img src="application/public/images/QQexpression/7.gif">
              <img src="application/public/images/QQexpression/8.gif">
              <img src="application/public/images/QQexpression/9.gif">
              <img src="application/public/images/QQexpression/10.gif">
              <img src="application/public/images/QQexpression/11.gif">
              <img src="application/public/images/QQexpression/12.gif">
              <img src="application/public/images/QQexpression/13.gif">
              <img src="application/public/images/QQexpression/14.gif">
              <img src="application/public/images/QQexpression/15.gif">
              <img src="application/public/images/QQexpression/16.gif">
            </div>
            <button type="button" class="btn btn-primary submit">添加</button>
            
          </div>
        </div>
      </form>
     </div>
      <?php endIf?>
    </div>
    <input type="hidden" id="torrentId" value="<?php echo $torrent['id']?>">
    <input type="hidden" id="baseUrl" value="<?php echo App::ins()->request->getBaseUrl()?>">
    <input type="hidden" id="username" value="<?php echo App::ins()->user->getName()?>">
    <div id="tpl" style="display:none">
   
    <div class="item" data-id="0">
        <h4 class="comment-head">#<span class="comment-floor">0</span><span class="text-primary comment-username">张三</span><small class="pull-right comment-add-time">2015-03-02</small></h4>
        <div class="clearfix">
          <div class="col-xs-2 avatar">
            <img src="application/public/images/avatar.jpg" class="img-responsive"/>
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
 <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.parse.min.js"></script>
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
		
	})
 </script>