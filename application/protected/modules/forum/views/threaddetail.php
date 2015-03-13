<nav class="forum-thread-nav" style="margin-bottom: 16px">
		<a href="<?php echo $this->createUrl('forum/thread/add', array('section_id' => $section['id']))?>" class="btn btn-success pull-left"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>发表主题</a>
	  <ul class="pagination">
	  	<li><a href="<?php echo $this->createUrl('forum/thread/list', array('section_id' => $section['id']))?>"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>返回</a></li>
	    <li>
	      <a href="#" aria-label="Previous">
	        <span aria-hidden="true">&laquo;</span>
	      </a>
	    </li>
	    <li><a href="#">1</a></li>
	    <li><a href="#">2</a></li>
	    <li><a href="#">3</a></li>
	    <li><a href="#">4</a></li>
	    <li><a href="#">5</a></li>
	    <li>
	      <a href="#" aria-label="Next">
	        <span aria-hidden="true">&raquo;</span>
	      </a>
	    </li>
	  </ul>
</nav>

<div class="row forum-thread-head"">
	<div class="col-md-2">
		<span>查看</span>
		<span class="text-danger"><?php echo $thread['view_count']+1?></span>
		<span>|</span>
		<span>回复</span>
		<span class="text-danger"><?php echo $thread['reply_count']?></span>
	</div>
	<div class="col-md-10">
		<a href="#" class="forum-thread-title"><?php echo $thread['title']?></a>
		<div class="pull-right">
			<a href="#" class="glyphicon glyphicon-arrow-left" aria-hidden="true" title="上一主题" style="cursor: pointer"></a>
			<a href="#" class="glyphicon glyphicon-arrow-right" aria-hidden="true" title="下一主题" style="cursor: pointer"></a>
		</div>
	</div>

</div>

<div id="forum-thread-reply-list">

<!-- 楼主部分 -->

<div class="row forum-thread-reply">
	<div class="col-md-2">
		<h4><strong><?php echo $thread['name']?></strong>(<?php echo $thread['role_name']?>)</h4>
	</div>
	<div class="col-md-10">
		<h4>发表于&nbsp;&nbsp;<em><?php echo date('Y-m-d H:i', $thread['add_time'])?></em>&nbsp;&nbsp;<a href="#"><small>只看该作者</small></a>&nbsp;&nbsp;<a href="#"><small>编辑</small></a><span class="pull-right"><i>楼主</i>&nbsp;&nbsp;<small title="输入层数后回车">电梯&nbsp;<input type="text" size="1" id="elevator"></small></span></h4>
	</div>
	
	<div class="col-md-2 forum-reply-user">
		<div><a href="#"><img src="<?php echo App::ins()->request->getBaseUrl()?>application/public/images/avatar.jpg" class="img-responsive"></a></div>
		<div>
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
	<?php if (count($appraiseList)):?>
	<div class="col-md-offset-2 col-md-10 bg-warning text-danger forum-thread-support-title"><h4><span class="glyphicon glyphicon-record" aria-hidden="true"></span>支持</h4></div>
	<div class="col-md-offset-2 col-md-10">
		<table class="table">
			<thead>
				<tr>
					<th>参与人数<em class="text-danger bg-warning"><?php echo count($appraiseList)?></em></th>
					<th>魔力<em class="text-danger bg-warning">22</em></th>
					<th>理由</th>
					<th style="text-align: right"><a href="#">收起</a></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($appraiseList as $appraise):?>
				<tr>
					<td><?php echo $appraise['name']?></td>
					<td><?php echo $appraise['award_value']?></td>
					<td colspan="2"><?php echo $appraise['reason']?></td>
				</tr>
				
			<?php endforeach?>
			</tbody>
		</table>
	</div>
	<?php endIf?>
	<div class="col-md-offset-2 col-md-10 forum-thread-action">
		<button class="btn btn-warning"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>支持</button>
		<button class="btn btn-warning"><span class="glyphicon glyphicon-bookmark" aria-hidden="true"></span>收藏</button>
	</div>

</div>

<!-- 回复开始 -->

<?php if (!empty($replyList)):?>
<?php foreach ($replyList as $reply):?>
<div class="row forum-thread-reply">
	
	<div class="col-md-2">
		<h4><strong><?php echo $reply['name']?></strong>(<?php echo $reply['role_name']?>)</h4>
	</div>
	<div class="col-md-10">
		<h4>发表于&nbsp;&nbsp;<em><?php echo date('Y-m-d H:i', $reply['add_time'])?></em>&nbsp;&nbsp;<a href="#"><small>只看该作者</small></a>&nbsp;&nbsp;<a href="#"><small>编辑</small></a><span class="pull-right"><i><?php echo $reply['floor']?></i>楼</span></h4>
	</div>
	
	<div class="col-md-2 forum-reply-user">
		<div><a href="#"><img src="<?php echo App::ins()->request->getBaseUrl()?>application/public/images/avatar.jpg" class="img-responsive"></a></div>
		<div>
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
	<div class="col-md-10">
		<?php echo $reply['content']?>
	</div>
	
	<div class="col-md-offset-2 col-md-10 forum-thread-reply-action">
		<div class="pull-left">
			<a href="#" style="margin-right: 20px"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>支持</a>
			<a href="#" style="margin-right: 20px"><span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>反对</a>
			<a href="#" style="margin-right: 20px"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>举报</a>
		</div>
	</div>
	
<?php if ($reply['reply_id'] != 0):?>
	<div class="col-md-offset-2 col-md-10 forum-thread-reply-reply">
		<div class="row">
			<div class="col-md-1">
				<img src="<?php echo App::ins()->request->getBaseUrl()?>application/public/images/avatar.jpg" class="img-responsive">
			</div>
			<div class="col-md-11">
				<a href="#"><?php echo $reply['name']?></a>：<?php echo $reply['content']?>
			</div>
			<div class="col-md-offset-1 col-md-10"><i class="pull-right"><span><?php echo date('Y-m-d H:i', $reply['add_time'])?></span><span class="forum-thread-reply-reply-reply">回复</span></i></div>
		</div>
	</div>
<?php endIf?>

	<div class="col-md-offset-2 col-md-10 forum-thread-reply-action">
		<?php if (!empty($reply['front_reply'])):?>
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
		<?php endIf?>
		<div class="pull-right"><a href="#"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>回复</a></div>
	</div>
</div>

<?php endForeach?>
<?php endIf?>


<div class="row forum-thread-reply">
	<div class="col-md-2">
		<h4><strong>xiaomiao</strong>(小学生)</h4>
	</div>
	<div class="col-md-10">
		<h4>发表于&nbsp;&nbsp;<em>2015-03-08 21:45</em>&nbsp;&nbsp;<a href="#"><small>只看该作者</small></a><span class="pull-right"><i>2</i>楼</span></h4>
	</div>
	
	<div class="col-md-2">
		<div><a href="#"><img src="<?php echo App::ins()->request->getBaseUrl()?>application/public/images/avatar.jpg" class="img-responsive"></a></div>
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
		<img src="<?php echo App::ins()->request->getBaseUrl()?>application/public/images/1.jpg" class="img-responsive" style="width: 600px">
	</div>
	
	<div class="col-md-offset-2 col-md-10 forum-thread-reply-action">
		<div class="pull-left">
			<a href="#" style="margin-right: 20px"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>支持</a>
			<a href="#"><span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>反对</a>
		</div>
		<div class="pull-right"><a href="#"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>举报</a></div>
	</div>
	
	<div class="col-md-offset-2 col-md-10 forum-thread-reply-reply">
		<div class="row">
			<div class="col-md-1">
				<img src="<?php echo App::ins()->request->getBaseUrl()?>application/public/images/avatar.jpg" class="img-responsive">
			</div>
			<div class="col-md-11">
				<a href="#">xiaomiao</a>：据说旧版固件7天左右。目前新版刚出，加了点功能，官方说4天。我现在用了2天掉了一个电，还行。先马克，还是有点贵，我对时间无所谓，反正有手表
			</div>
			<div class="col-md-offset-1 col-md-10"><i class="pull-right"><span>2015-03-09 00:03</span><span class="forum-thread-reply-reply-reply">回复</span></i></div>
		</div>
		<div class="row">
			<div class="col-md-1">
				<img src="<?php echo App::ins()->request->getBaseUrl()?>application/public/images/avatar.jpg" class="img-responsive">
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
</div>
<nav class="forum-thread-nav" style="margin: 20px 0">
		<a href="<?php echo $this->createUrl('forum/thread/add', array('section_id' => $section['id']))?>" class="btn btn-success pull-left"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>发表主题</a>
	  <ul class="pagination">
	  	<li><a href="<?php echo $this->createUrl('forum/thread/list', array('section_id' => $section['id']))?>"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>返回</a></li>
	    <li>
	      <a href="#" aria-label="Previous">
	        <span aria-hidden="true">&laquo;</span>
	      </a>
	    </li>
	    <li><a href="#">1</a></li>
	    <li><a href="#">2</a></li>
	    <li><a href="#">3</a></li>
	    <li><a href="#">4</a></li>
	    <li><a href="#">5</a></li>
	    <li>
	      <a href="#" aria-label="Next">
	        <span aria-hidden="true">&raquo;</span>
	      </a>
	    </li>
	  </ul>
</nav>

<div class="row">
	<div class="col-md-offset-3 col-md-6">
		<form class="form-horizontal">
		  <div class="form-group">
		    <div class="col-sm-12">
		      <script id="uecontainer" name="content" type="text/plain"></script>
		    </div>
		  </div>

		  <div class="form-group">
		    <div class="col-sm-offset-5 col-sm-7">
		      <button type="button" id="submit" class="btn btn-success">回复</button>
		      <a href="<?php echo $this->createUrl('forum/reply/add', array('section_id' => $_GET['section_id'], 'thread_id' => $_GET['thread_id']))?>" type="button" class="btn btn-default btn-sm pull-right">高级模式</a>
		    </div>
		  </div>
		</form>
	</div>
</div>
<input type="hidden" id="add-view" value="<?php echo $this->createUrl('forum/thread/addview')?>">
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.config.thread-detail.js"></script>
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.all.min.js"></script>
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
		var content = ue.getContent();
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

	//添加浏览量
	window.onload = function(){
		var referrer = "http://"+location.hostname+$("#forum-nav").children("a").eq(1).attr("href");
		console.log(referrer);
		console.log(document.referrer);
		if (referrer === document.referrer){
			$.ajax({
				url: $("#add-view").val(),
				type: "POST",
				data: hrefArr[1]+"&addview=1",
				dataType: "json",
				success: function(data){
					console.log(data);
				}
			})
		}
	}
	
	  
  </script>