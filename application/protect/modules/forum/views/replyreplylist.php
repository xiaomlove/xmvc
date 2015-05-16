<?php if(!empty($data)):?>
<?php foreach ($data as $reply):?>
<div class="row" data-userid="<?php echo $reply['userid']?>" data-username=<?php echo $reply['username']?>>
	<div class="col-md-1" style="margin-right:-15px">
		<img src="<?php echo App::ins()->request->getBaseUrl().$reply['avatar_url']?>" class="img-responsive">
	</div>
	<div class="col-md-11">
		<a href="#"><?php echo $reply['username']?></a>：<?php echo $reply['content']?>
	</div>
	<div class="col-md-offset-1 col-md-10"><i class="pull-right"><span><?php echo date('Y-m-d H:i', $reply['add_time'])?></span><span class="forum-thread-reply-reply-reply">回复</span></i></div>
</div>
<?php endForeach?>
<?php endIf?>