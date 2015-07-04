<nav class="forum-thread-nav" style="margin-bottom: 16px">
	  	
	  <a href="<?php echo $this->createUrl('forum/thread/add', array('section_id' => $section['id']))?>" class="btn btn-info pull-left"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>发表主题</a>
	  <?php echo $navHtml?>
</nav>

<div class="row forum-thread-head">
	<div class="col-md-2">
		<span>查看</span>
		<span class="text-danger"><?php echo $thread['view_count']+1?></span>
		<span>|</span>
		<span>回复</span>
		<span class="text-danger"><?php echo $thread['reply_count']?></span>
	</div>
	<div class="col-md-10 text-left">
		<a href="#" class="forum-thread-title"><?php echo $thread['state'] == \application\protect\models\ForumthreadModel::STATE_DRAFT ? "【草稿】".$thread['title'] : $thread['title']?></a>
		<div class="pull-right">
			<a href="#" class="glyphicon glyphicon-arrow-left" aria-hidden="true" title="上一主题" style="cursor: pointer"></a>
			<a href="#" class="glyphicon glyphicon-arrow-right" aria-hidden="true" title="下一主题" style="cursor: pointer"></a>
		</div>
	</div>

</div>

<div id="forum-thread-reply-list">

<!-- 楼主部分 -->
<?php if (!empty($thread)):?>
<div class="row forum-thread-reply">
	<div class="col-md-2">
		<h4><strong><?php echo $thread['name']?></strong>(<?php echo $thread['role_name']?>)</h4>
	</div>
	<div class="col-md-10">
		<h4>
			发表于&nbsp;&nbsp;<em><?php echo date('Y-m-d H:i', $thread['add_time'])?></em>
			&nbsp;&nbsp;<a href="#"><small>只看该作者</small></a>
			&nbsp;&nbsp;<a href="<?php echo $this->createUrl('forum/thread/edit', array('section_id' => $section['id'], 'thread_id' => $thread['id']))?>"><small>编辑</small></a>
			<span class="pull-right"><i>楼主</i>&nbsp;&nbsp;<small title="输入层数后回车">电梯&nbsp;<input type="text" size="1" id="elevator"></small></span>
		</h4>
	</div>
	
	<div class="col-md-2 hidden-sm hidden-xs forum-reply-user">
		<div><a href="#"><img src="<?php echo \framework\App::ins()->request->getBaseUrl()?>application/assets/images/avatar.jpg" class="img-responsive"></a></div>
		<div class="hidden-md hidden-sm hidden-xs">
			<table class="table user-info-table">
				<tbody>
					<tr>
						<td><p><a href="#"><?php echo $this->getSize($thread['uploaded'])?></a></p><p>上传</p></td>
						<td><p><a href="#"><?php echo $this->getSize($thread['downloaded'])?></a></p><p>下载</p></td>
						<td><p><a href="#"><?php echo (!empty($thread['downloaded'])) ? number_format($thread['uploaded']/$thread['downloaded'], 2, '.', '') : 0?></a></p><p>分享率</p></td>
					</tr>
					<tr>
						<td><p><a href="#"><?php echo $thread['thread_count']?></a></p><p>主题</p></td>
						<td><p><a href="#"><?php echo $thread['user_info_reply_count']?></a></p><p>回复</p></td>
						<td><p><a href="#"><?php echo $thread['comment_count']?></a></p><p>评论</p></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="forum-thread-user-action">
			<a href="#">私信</a>
			<a href="#">加好友</a>
		</div>
	</div>
	<div class="col-md-10">
		<?php echo $thread['content']?>
	</div>
	
	<!-- 支持部分 -->
	<div id="appraise" class="hidden-sm hidden-xs">
	<?php if (count($appraiseList)):?>
	<div class="col-md-offset-2 col-md-10 bg-warning text-danger forum-thread-support-title"><h4><span class="glyphicon glyphicon-record" aria-hidden="true"></span>支持</h4></div>
	<div class="col-md-offset-2 col-md-10">
		<table class="table">
			<thead>
				<tr>
					<th>参与人数<em class="text-danger bg-warning appraise-user-count"><?php echo count($appraiseList)?></em></th>
					<th>魔力<em class="text-danger bg-warning appraise-bonus-count">
					<?php
						$sum = 0;
						$supported = FALSE;
						$userId = \framework\App::ins()->user->getId();
						foreach ($appraiseList as $appraise)
						{
							if (!$supported && $appraise['user_id'] == $userId)
							{
								$supported = TRUE;
							}
							$sum += $appraise['award_value'];
						}
						unset($appraise);
						reset($appraiseList);
						echo $sum;			
					?>
					</em></th>
					<th>理由</th>
					<th style="text-align: right"><a href="#">收起</a></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($appraiseList as $appraise):?>
				<tr>
					<td><a href="#"><?php echo $appraise['name']?></a></td>
					<td><?php echo $appraise['award_value']?></td>
					<td colspan="2"><?php echo $appraise['reason']?></td>
				</tr>
				
			<?php endforeach?>
			</tbody>
		</table>
	</div>
	
	<?php endIf?>
	</div>
	<div class="col-md-offset-2 col-md-10 forum-thread-action">
		<button class="btn btn-warning" data-toggle="modal" data-target="#support" data-action="support" data-supported="<?php echo isset($supported) && $supported ? 1: 0?>"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>支持</button>
		<button class="btn btn-warning" data-action="bookmark"><span class="glyphicon glyphicon-bookmark" aria-hidden="true"></span>收藏<?php echo empty($thread['bookmark_count']) ? "" : "<span class=\"bookmark_count\">".$thread['bookmark_count']."</span>"?></button>
	</div>

</div>
<?php endIf?>
<!-- 回复开始 -->

<?php if (!empty($replyList)):?>
<?php foreach ($replyList as $reply):?>
<div class="row forum-thread-reply" data-id="<?php echo $reply['id']?>">
	
	<div class="col-md-2">
		<h4><strong><?php echo $reply['name']?></strong>(<?php echo $reply['role_name']?>)</h4>
	</div>
	<div class="col-md-10">
		<h4>
			发表于&nbsp;&nbsp;<em><?php echo date('Y-m-d H:i', $reply['add_time'])?></em>
			&nbsp;&nbsp;<a href="#"><small>只看该作者</small></a>
			<?php if ($reply['user_id'] == $userId):?>
			&nbsp;&nbsp;<a href="<?php echo $this->createUrl('forum/reply/edit', array('section_id' => $section['id'], 'thread_id' => $thread['id'], 'reply_id' => $reply['id']))?>"><small>编辑</small></a>
			<?php endIf?>
			&nbsp;&nbsp;<a href="#"><small>举报</small></a>
			<span class="pull-right"><i><?php echo $reply['floor']?></i>楼</span>
		</h4>
	</div>
	
	<div class="col-md-2 hidden-sm hidden-xs forum-reply-user">
		<div><a href="#"><img src="<?php echo \framework\App::ins()->request->getBaseUrl()?>application/assets/images/avatar.jpg" class="img-responsive"></a></div>
		<div class="hidden-md hiden-sm hidden-xs">
			<table class="table user-info-table">
				<tbody>
					<tr>
						<td><p><a href="#"><?php echo $this->getSize($reply['uploaded'])?></a></p><p>上传</p></td>
						<td><p><a href="#"><?php echo $this->getSize($reply['downloaded'])?></a></p><p>下载</p></td>
						<td><p><a href="#"><?php echo !empty($reply['downloaded']) ? number_format($reply['uploaded']/$reply['downloaded'], 2, '.', '') : 0?></a></p><p>分享率</p></td>
					</tr>
					<tr>
						<td><p><a href="#"><?php echo $reply['thread_count']?></a></p><p>主题</p></td>
						<td><p><a href="#"><?php echo $reply['user_info_reply_count']?></a></p><p>回复</p></td>
						<td><p><a href="#"><?php echo $reply['comment_count']?></a></p><p>评论</p></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="forum-thread-user-action">
			<a href="#">私信</a>
			<a href="#">加好友</a>
		</div>
	</div>
	<div class="col-md-10" style="margin-bottom:20px">
		<?php echo $reply['content']?>
	</div>
	

	<div class="col-md-offset-2 col-md-10 forum-thread-reply-reply">
	<?php if ($reply['reply_count'] > 0) echo $reply['front_reply']?>
		
	</div>


	<div class="col-md-offset-2 clearfix col-md-10 forum-thread-reply-action">
		<?php if (!empty($reply['front_reply'])):?>
		<div class="pull-left forum-thread-reply-reply-nav">
		<?php if ($reply['reply_count'] > 5) echo '还有'.($reply['reply_count']-5).'条回复，<a href="javascript:;" class="view-more">点击查看</a>'?>
		<!-- 
			<nav>
			  <ul class="pagination pagination-sm">
			    <li>
			      <a href="#" aria-label="Previous">
			        <span aria-hidden="true">&laquo;</span>
			      </a>
			    </li>
			    <li><a href="#">1</a></li>
			    <li><a href="#">2</a></li>
			    <li>
			      <a href="#" aria-label="Next">
			        <span aria-hidden="true">&raquo;</span>
			      </a>
			    </li>
			  </ul>
			</nav>
		 -->
		</div>
		<?php endIf?>
		<div class="pull-right form-thread-reply-btn"><a href="javascript:;"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>回复</a></div>
	</div>
	<div class="col-md-offset-2 col-md-10 forum-reply-reply-form-wrap hide">
		<div contenteditable="true" class="forum-reply-reply-form"></div>
		<div><a class="btn btn-default btn-sm pull-right submit">提交</a></div>
	</div> 
</div>

<?php endForeach?>
<?php endIf?>

<!--
<div class="row forum-thread-reply">
	<div class="col-md-2">
		<h4><strong>xiaomiao</strong>(小学生)</h4>
	</div>
	<div class="col-md-10">
		<h4>发表于&nbsp;&nbsp;<em>2015-03-08 21:45</em>&nbsp;&nbsp;<a href="#"><small>只看该作者</small></a><span class="pull-right"><i>2</i>楼</span></h4>
	</div>
	
	<div class="col-md-2">
		<div><a href="#"><img src="<?php echo \framework\App::ins()->request->getBaseUrl()?>application/assets/images/avatar.jpg" class="img-responsive"></a></div>
		<div>
			<table class="table user-info-table">
				<tbody>
					<tr>
						<td><p><a href="#">45GB</a></p><p>上传</p></td>
						<td><p><a href="#">45GB</a></p><p>下载</p></td>
						<td><p><a href="#">1.35</a></p><p>分享率</p></td>
					</tr>
					<tr>
						<td><p><a href="#">20</a></p><p>主题</p></td>
						<td><p><a href="#">10</a></p><p>回复</p></td>
						<td><p><a href="#">23</a></p><p>评论</p></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="forum-thread-user-action">
			<a href="#">私信</a>
			<a href="#">加好友</a>
		</div>
	</div>
	<div class="col-md-10">
		好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！好像很屌的样子！
		<img src="<?php echo \framework\App::ins()->request->getBaseUrl()?>application/assets/images/1.jpg" class="img-responsive" style="width: 600px">
	</div>
	
	<div class="col-md-offset-2 col-md-10 forum-thread-reply-action" style="margin-top: 20px;margin-bottom: 20px">
		<div class="pull-left">
			<a href="#" style="margin-right: 20px"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>支持</a>
			<a href="#"><span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>反对</a>
		</div>
		<div class="pull-right"><a href="#"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>举报</a></div>
	</div>
	
	<div class="col-md-offset-2 col-md-10 forum-thread-reply-reply">
		<div class="row">
			<div class="col-md-1">
				<img src="<?php echo \framework\App::ins()->request->getBaseUrl()?>application/assets/images/avatar.jpg" class="img-responsive">
			</div>
			<div class="col-md-11">
				<a href="#">xiaomiao</a>：据说旧版固件7天左右。目前新版刚出，加了点功能，官方说4天。我现在用了2天掉了一个电，还行。先马克，还是有点贵，我对时间无所谓，反正有手表
			</div>
			<div class="col-md-offset-1 col-md-10"><i class="pull-right"><span>2015-03-09 00:03</span><span class="forum-thread-reply-reply-reply">回复</span></i></div>
		</div>
		<div class="row">
			<div class="col-md-1">
				<img src="<?php echo \framework\App::ins()->request->getBaseUrl()?>application/assets/images/avatar.jpg" class="img-responsive">
			</div>
			<div class="col-md-11">
				<a href="#">xiaomiao</a>&nbsp;&nbsp;回复&nbsp;&nbsp;<a href="#" class="left">xiaomlove</a>：据说旧版固件7天左右。目前新版刚出，加了点功能，官方说4天。我现在用了2天掉了一个电，还行。先马克，还是有点贵，我对时间无所谓，反正有手表
			</div>
			<div class="col-md-offset-1 col-md-10"><i class="pull-right"><span>2015-03-09 00:03</span><span class="forum-thread-reply-reply-reply">回复</span></i></div>
		</div>
		
	</div>
	
	<div class="col-md-offset-2 col-md-10 forum-thread-reply-action">
		<div class="pull-left">
			<nav>
			  <ul class="pagination pagination-sm">
			    <li>
			      <a href="#" aria-label="Previous">
			        <span aria-hidden="true">&laquo;</span>
			      </a>
			    </li>
			    <li><a href="#">1</a></li>
			    <li><a href="#">2</a></li>
			    <li>
			      <a href="#" aria-label="Next">
			        <span aria-hidden="true">&raquo;</span>
			      </a>
			    </li>
			  </ul>
			</nav>
		</div>
		<div class="pull-right"><a href="#"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>回复</a></div>
	</div>
</div>
-->
</div>
<nav class="forum-thread-nav" style="margin: 20px 0">
		<a href="<?php echo $this->createUrl('forum/thread/add', array('section_id' => $section['id']))?>" class="btn btn-info pull-left"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>发表主题</a>

		<?php echo $navHtml?>
</nav>

<div class="row">
	<div class="col-md-offset-3 col-md-6 col-sm-12">
		<form class="form-horizontal">
		  <div class="form-group">
		    <div class="col-xs-12">
		      <script id="uecontainer" name="content" type="text/plain"></script>
		    </div>
		  </div>

		  <div class="form-group">
		    <div class="col-xs-12 text-center">
		      <button type="button" id="submit" class="btn btn-success">回复</button>
		      <a href="<?php echo $this->createUrl('forum/reply/add', array('section_id' => $_GET['section_id'], 'thread_id' => $_GET['thread_id']))?>" type="button" class="btn btn-default btn-sm pull-right">高级模式</a>
		    </div>
		  </div>
		</form>
	</div>
</div>

<div class="modal fade" id="support" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" style="text-align: center">奖励魔力—支持作者</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal">
          <div class="form-group" data-type="bonus">
            <label for="recipient-name" class="col-sm-3 control-label">由系统自动加上：</label>
            <div class="col-sm-6">
            	<input type="text" class="form-control" data-type="system" readonly>
            </div>
            <div class="col-sm-3">
            	<div class="btn-group" role="group">
				    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				     请选择
				      <span class="caret"></span>
				    </button>
				    <ul class="dropdown-menu" role="menu">
				      <li><a>5</a></li>
				      <li><a>10</a></li>
				    </ul>
				 </div>
            </div>
          </div>
          <div class="form-group" data-type="bonus">
            <label for="recipient-name" class="col-sm-3 control-label">从自己魔力里扣：</label>
            <div class="col-sm-6">
            	<input type="text" class="form-control" data-type="self" readonly>
            </div>
            <div class="col-sm-3">
            	<div class="btn-group" role="group">
				    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				     请选择
				      <span class="caret"></span>
				    </button>
				    <ul class="dropdown-menu" role="menu">
				      <li><a>100</a></li>
				      <li><a>200</a></li>
				      <li><a>500</a></li>
				      <li><a>1000</a></li>
				    </ul>
				 </div>
            </div>
          </div>
          <div class="form-group" data-type="reason">
            <label for="" class="col-sm-3 control-label">支持理由：</label>
            <div class="col-sm-6">
            	<input type="text" class="form-control" data-type="reason">
            </div>
            <div class="col-sm-3">
            	<div class="btn-group" role="group">
				    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				     可选择
				      <span class="caret"></span>
				    </button>
				    <ul class="dropdown-menu" role="menu">
				      <li><a>技术帖必须支持！</a></li>
				      <li><a>有你更精彩！</a></li>
				    </ul>
				 </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer" style="text-align: center">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary">确定</button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="add-bookmark" value="<?php echo $this->createUrl('bookmark/add')?>">
<input type="hidden" id="add-support" value="<?php echo $this->createUrl('forum/thread/addappraise')?>">
<input type="hidden" id="add-view" value="<?php echo $this->createUrl('forum/thread/addview')?>">
<input type="hidden" id="add-reply-reply" value="<?php echo $this->createUrl('forum/replyreply/add')?>">
<input type="hidden" id="view-more" value="<?php echo $this->createUrl('forum/replyreply/list')?>">

<?php echo $this->getScript('application/assets/lib/ueditor/ueditor.config.thread-detail.js')?>
  <script src="<?php echo \framework\App::ins()->request->getBaseUrl()?>application/assets/lib/ueditor/ueditor.all.min.js"></script>
  <script type="text/javascript">
  var ue = UE.getEditor('uecontainer');
  ue.ready(function() {
     
      //获取html内容，返回: <p>hello</p>
      //var html = ue.getContent();
      //获取纯文本内容，返回: hello
      //var txt = ue.getContentTxt();
  });
  
	var $submit = $("#submit");
	var href = $submit.next().attr("href");
	var hrefArr = href.split("?");
	
	$submit.on("click", function(e){
		var content = encodeURIComponent(ue.getContent());
		if (content == ""){
			alert("请输入内容");
			return;
		}
		$.ajax({
			url: hrefArr[0],
			type: "POST",
			data: hrefArr[1]+"&content="+content+"&quickReply=1",
			dataType: 'json',
			beforeSend: function(){$submit.text("发表中...").attr("disabled", "disabled")},
			success: function(data){
//				console.log(data);return;
				if (data.code == 1){
					$("#forum-thread-reply-list").append(data.msg);
					$submit.removeAttr("disabled").text("发表");
					ue.setContent('');
				}else{
					$submit.text(data.msg);
				}
			}
		})
		
	});

	//添加支持
	$action = $(".forum-thread-action").find("button[data-supported]");
	$action.on("mousedown", function(e){
		if ($(this).attr("data-supported") > 0){
			alert("你已经支持过了");
			e.stopPropagation();
			return;
		}
	})
	var $support = $("#support");
	$support.find("div[data-type=bonus]").find("li").on("click", function(){
		$support.find("input").not("[data-type=reason]").removeClass("selected").val("");
		$(this).parents(".form-group").find("input:first").addClass("selected").val($(this).children("a").text());
	});
	$support.find("div[data-type=reason]").find("li").on("click", function(){
		$(this).parents(".form-group").find("input:first").val($(this).children("a").text());
	})
	var $supportButton =  $support.find(".modal-footer").children("button:last");
	$supportButton.on("click", function(){
		var reason = $support.find("input[data-type=reason]").val();
		var $bonus = $support.find("input.selected");
		var bonus = $bonus.val();
		var type = $bonus.attr("data-type");
		var $appraise = $("#appraise");
		if ($appraise.find("table").length){
			var isFirst = 0;//不是第一个支持
		}else{
			var isFirst = 1;
		}
		if ($.trim(bonus) == ""){
			alert("请选择魔力值");
			return;
		}
		if ($.trim(reason) == ""){
			alert("请填写理由！");
			return;
		}
		$.ajax({
			url: $("#add-support").val(),
			type: "POST",
			data: hrefArr[1]+"&reason="+encodeURIComponent(reason)+"&bonus="+bonus+"&type="+type+"&addappraise=1&isFirst="+isFirst,
			dataType: "json",
			beforeSend: function(){$supportButton.text("支持中...")},
			success: function(data){
				if (data.code == 1){
					$supportButton.text("确定").prev().trigger("click");
					if (!isFirst){
						//不是第一个
						$appraise.find("tbody").append(data.msg);
						$appraise.find(".appraise-user-count").text(function(text){
							return parseInt(text)+1;
						});
						$appraise.find(".appraise-bonus-count").text(function(text){
							return parseInt(text)+bonus;
						})
					}else{
						//第一个，直接插入
						$appraise.append(data.msg);
					}
					$action.attr("data-supported", 1);
				}else{
					$supportButton.text(data.msg).attr("disabled", "disabled");
				}
			}
		})
		
	});

	//收藏
	var $bookmark = $(".forum-thread-action").find("button[data-action=bookmark]");
	$bookmark.on("mousedown", function(e){
		if ($bookmark.attr("data-bookmarked") > 0){
			alert("你已经收藏了");
			return;
		}
		var idArr = hrefArr[1].split("&");
		var resource_id = idArr[1].split("=")[1];
		$.ajax({
			url: $("#add-bookmark").val(),
			type: "POST",
			dataType: 'json',
			data: "type=1&resource_id="+resource_id,
			success: function(data){
				if (data.code == 1){
					alert(data.msg);
					$bookmark.attr("data-bookmarked", 1);
					var $bookmark_count = $(".bookmark_count");
					if ($bookmark_count.length){
						$bookmark_count.text(function(text){
							return parseInt(text)+1;
						});
					}else{
						$bookmark.append("<span class=\"bookmark_count\">1</span>");
					}
				}else if(data.code == 2){
					alert(data.msg);
				}else{
					console.log(data.msg);
				}
			}
		})
	});

	//楼层回复
	var $replyList = $("#forum-thread-reply-list");
	$replyList.on("mousedown", ".forum-thread-reply-action .form-thread-reply-btn", function(e){
		$(this).parent().next().toggleClass("hide");
		$parent = $(this).parents("[data-id]");
		$parent.find(".submit").removeAttr("data-userid").removeAttr("data-username");
		$parent.find(".forum-reply-reply-form").text("");
	});
	$replyList.on("mousedown", ".forum-thread-reply-reply-reply", function(e){
		var $dataUser = $(this).parents("[data-userid]");
		var $parentRow = $dataUser.parents("[data-id]");
		$parentRow.find(".submit").attr("data-userid", $dataUser.attr("data-userid")).attr("data-username", $dataUser.attr("data-username"));
		$parentRow.find(".forum-reply-reply-form-wrap").removeClass("hide").find(".forum-reply-reply-form").text("回复  "+$dataUser.attr("data-username")+"：");
		
	});
	$replyList.on("mousedown", ".forum-reply-reply-form-wrap .submit", function(e){
		var $content = $(this).parent().prev();
		var content = $content.text();
		var $submitBtn = $(this);
		if ($.trim(content) == ""){
			alert("请输入内容");
			return;
		}
		var $parentRow = $(this).parents("[data-id]");
		var reply_id = $parentRow.attr("data-id");
		var sectionThreadId = hrefArr[1];
		var $replyWrap = $parentRow.find(".forum-thread-reply-reply");
		var extra = "";
		if ($submitBtn.attr("data-userid")){
			extra = "&to_user_id="+$submitBtn.attr("data-userid")+"&to_user_name="+$submitBtn.attr("data-username");
		}
//		console.log($("#add-reply-reply").val());return;
		$.ajax({
			url: $("#add-reply-reply").val(),
			data: sectionThreadId+"&reply_id="+reply_id+"&content="+encodeURIComponent(content)+extra,
			dataType: "json",
			type: "POST",
			beforeSend: function(){$submitBtn.text("提交中...").attr("disabled", "disabled")},
			success: function(data){
				if (data.code == 1){
					$content.text("");
					$submitBtn.text("提交").removeAttr("disabled").parent().parent().addClass("hide");
					$replyWrap.append(data.msg);
				}else{
					$submitBtn.text(data.msg);
				}
			}
		})
		
		
	});

	//查看楼中楼更多
	$replyList.on("mousedown", ".view-more", function(e){
		var $row = $(this).parents("[data-id]");
		var replyId = $row.attr("data-id");
		var $replyReplyList = $row.find(".forum-thread-reply-reply");
		var offset = $replyReplyList.children(".row").length;
		$.ajax({
			url: $("#view-more").val(),
			data: "reply_id="+replyId+"&offset="+offset+"&viewmore=1&first=1",
			type: "POST",
			dataType: "json",
			success: function(data){
				if (data.code == 1){
					$replyReplyList.append(data.data);
					$row.find(".forum-thread-reply-reply-nav").html(data.nav);
				}else{
					console.log(data);
				}
			}
		})
	});

	//楼中楼分页查看
	$replyList.on("mousedown", ".pagination a", function(e){
		
		var $row = $(this).parents("[data-id]");
		var replyId = $row.attr("data-id");
		var $replyReplyList = $row.find(".forum-thread-reply-reply");
		var $active = $(this).parent().parent().find(".active");
		if ($(this).attr("aria-label") == "Previous"){
			page = parseInt($active.children("a").text())-1;
		}else if($(this).attr("aria-label") == "Next"){
			page = parseInt($active.children("a").text())+1;
		}else{
			page = $(this).text();
		}
// 		alert(page);
		$.ajax({
			url: $("#view-more").val(),
			data: "reply_id="+replyId+"&viewmore=1&page="+page,
			type: "POST",
			dataType: "json",
			success: function(data){
				if (data.code == 1){
					$replyReplyList.html(data.data);
					$row.find(".forum-thread-reply-reply-nav").html(data.nav);
				}else{
					console.log(data);
				}
			}
		})
	});

	//添加浏览量
	window.onload = function(){
		var referrer1 = "http://"+location.hostname+$("#forum-nav").children("a").eq(0).attr("href");
		var referrer2 = "http://"+location.hostname+$("#forum-nav").children("a").eq(1).attr("href");
		var referrer = document.referrer;
		if (referrer1 === referrer || referrer2 === referrer || (referrer.indexOf(referrer2) > -1 && referrer.indexOf("&extra=") == -1)){
			$.ajax({
				url: $("#add-view").val(),
				type: "POST",
				data: hrefArr[1]+"&addview=1",
				dataType: "html",
				success: function(data){
					console.log(data);
				}
			})
		}
	}
	
	window.onload = function() {
		document.getElementById('edui1').removeAttribute('style');
	}
  </script>