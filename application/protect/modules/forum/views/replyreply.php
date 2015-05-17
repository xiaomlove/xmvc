<div class="row" data-userid="<?php echo $userInfo['id']?>" data-username=<?php echo $userInfo['name']?>>
	<div class="col-md-1" style="margin-right:-15px">
		<img src="<?php echo \framework\App::ins()->request->getBaseUrl().$userInfo['avatar_url']?>" class="img-responsive">
	</div>
	<div class="col-md-11">
		<a href="#"><?php echo $userInfo['name']?></a>：<?php echo $data['content']?>
	</div>
	<div class="col-md-offset-1 col-md-10"><i class="pull-right"><span><?php echo date('Y-m-d H:i', $_SERVER['REQUEST_TIME'])?></span><span class="forum-thread-reply-reply-reply">回复</span></i></div>
</div>
