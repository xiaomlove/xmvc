<?php foreach ($buildingList as $building):?>	
	<div class="row comment-item" data-building-id="<?php echo $building['id']?>">
        <div class="col-md-12 comment-main-header">
            <h5 class="">
                <span class="comment-username"><?php echo $building['user_name']?></span>
                <span class="comment-time pull-right text-muted">发表于<?php echo date('Y-m-d H:i:s', $building['add_time'])?></span>
            </h5>
        </div>
       
        <div class="col-md-2 comment-avatar">
            <a href="#"><img src="/application/assets/images/avatar.jpg" class="img-responsive"/></a>
        </div>
        <div class="col-md-10 comment-body">
            <div class="comment-text lead"><?php echo $building['content']?></div>
            <div class="comment-action clearfix" data-floor-id="<?php echo $building['id']?>">
                <div class="pull-right">
                    <a class="comment-up" href="#">支持(<span class="comment-up-count"><?php echo $building['up_count']?></span>)</a>
                    <a class="comment-down" href="#">反对(<span class="comment-down-count"><?php echo $building['down_count']?></span>)</a>
                    <a href="#" class="comment-report">举报</a>
                    <a href="#" class="comment-reply">回复</a>
                </div>
            </div>
        </div>
    </div>
  <?php endForeach?>