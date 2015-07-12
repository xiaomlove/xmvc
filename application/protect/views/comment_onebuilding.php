 	<?php $floorId = array_pop($building['floors'])?>
 	<div class="row comment-item" data-building-id="<?php echo $building['id']?>">
        <div class="col-md-12 comment-main-header">
            <h5 class="">
                <span class="comment-username"><?php echo $building['position']?>楼</span>
                <span class="comment-username"><?php echo $floorList[$floorId]['user_name']?></span>
                <span class="comment-address text-muted">来自[<?php echo $floorList[$floorId]['address']?>]</span>
                <span class="comment-time pull-right text-muted">发表于<?php echo date('Y-m-d H:i:s', $floorList[$floorId]['add_time'])?></span>
            </h5>
        </div>
       
        <div class="col-md-1 comment-avatar">
            <a href="#"><img src="/Public/images/1.jpg" width="80" /></a>
        </div>
        <div class="col-md-11 comment-body">
            <div class="comment-quote-wrap">
                <?php echo !empty($building['floors']) ? renderQuote($building['floors'], $floorList) : ''?>
            </div>
            <div class="comment-text lead"><?php echo $floorList[$floorId]['content']?></div>
            <div class="comment-action clearfix" data-floor-id="<?php echo $floorList[$floorId]['id']?>">
                <div class="pull-right">
                    <a class="comment-up" href="#">支持(<span class="comment-up-count"><?php echo $floorList[$floorId]['up_count']?></span>)</a>
                    <a class="comment-down" href="#">反对(<span class="comment-down-count"><?php echo $floorList[$floorId]['down_count']?></span>)</a>
                    <a href="#" class="comment-report">举报</a>
                    <a href="#" class="comment-reply">回复</a>
                </div>
            </div>
        </div>
    </div>