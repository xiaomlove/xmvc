<div class="row" data-floor="<?php echo $data['floor']?>">
	<div class="col-md-1">
		<img src="<?php echo App::ins()->request->getBaseUrl().$userInfo['avatar_url']?>" class="img-responsive">
	</div>
	<div class="col-md-11">
		<a href="#"><?php echo $userInfo['name']?></a>：<?php echo $data['content']?>
	</div>
	<div class="col-md-offset-1 col-md-10"><i class="pull-right"><span><?php echo date('Y-m-d H:i', $_SERVER['REQUEST_TIME'])?></span><span class="forum-thread-reply-reply-reply">回复</span></i></div>
</div>