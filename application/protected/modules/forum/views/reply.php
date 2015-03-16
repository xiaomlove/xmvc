<div class="row forum-thread-reply" data-id="<?php echo $replyId?>">
	<div class="col-md-2">
		<h4><strong><?php echo $userInfo['name']?></strong>(<?php echo $userInfo['roleName']?>)</h4>
	</div>
	<div class="col-md-10">
		<h4>发表于&nbsp;&nbsp;<em><?php echo date('Y-m-d H:i', $_SERVER['REQUEST_TIME'])?></em>&nbsp;&nbsp;<a href="#"><small>只看该作者</small></a><span class="pull-right"><i><?php echo $maxFloor+1?></i>楼</span></h4>
	</div>
	
	<div class="col-md-2">
		<div><a href="#"><img src="<?php echo App::ins()->request->getBaseUrl().$userInfo['avatar_url']?>" class="img-responsive"></a></div>
		<div>
			<table class="table user-info-table">
				<tbody>
					<tr>
						<td><p><a href="#"><?php echo $this->getSize($userInfo['downloaded'])?></a></p><p>上传</p></td>
						<td><p><a href="#"><?php echo $this->getSize($userInfo['uploaded'])?></a></p><p>下载</p></td>
						<td><p><a href="#"><?php echo number_format($userInfo['uploaded']/$userInfo['downloaded'], 2, '.', '')?></a></p><p>分享率</p></td>
					</tr>
					<tr>
						<td><p><a href="#"><?php echo $userInfo['thread_count']?></a></p><p>主题</p></td>
						<td><p><a href="#"><?php echo $userInfo['reply_count']?></a></p><p>回复</p></td>
						<td><p><a href="#"><?php echo $userInfo['comment_count']?></a></p><p>评论</p></td>
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
		<?php echo $content?>
	</div>
	
	<div class="col-md-offset-2 col-md-10 forum-thread-reply-action" style="margin-top: 20px;margin-bottom: 20px">
		<div class="pull-left">
			<a href="#" style="margin-right: 20px"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>支持</a>
			<a href="#" style="margin-right: 20px"><span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>反对</a>
			<a href="#" style="margin-right: 20px"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>举报</a>
		</div>
	</div>
	
	<div class="col-md-offset-2 col-md-10 forum-thread-reply-reply">

	</div>
	<div class="col-md-offset-2 col-md-10 forum-thread-reply-action">
		<div class="pull-right form-thread-reply-btn"><a href="javascript:;"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>回复</a></div>
	</div>
	<div class="col-md-offset-2 col-md-10 forum-reply-reply-form-wrap hide">
		<div contenteditable="true" class="forum-reply-reply-form"></div>
		<div><a class="btn btn-default btn-sm pull-right submit">提交</a></div>
	</div> 
</div>
