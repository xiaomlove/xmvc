	<h1 class="page-header text-center"><a href="<?php echo $this->createUrl('torrent/detail', array('id' => $torrentInfo['id']))?>"><?php echo $torrentInfo['name']?></a></h1>
    <div id="comment-list">
    <?php if (!empty($buildingList)):?>
   
    <?php foreach ($buildingList as $building):?>
    <?php $floorId = array_pop($building['floors'])?>
    <div class="row comment-item" data-building-id="<?php echo $building['id']?>">
        <div class="col-md-12 comment-main-header">
            <h5 class="">
                <span class="comment-username"><?php echo $building['position']?>楼</span>
                <span class="comment-username"><?php echo $floorList[$floorId]['user_name']?></span>
                <span class="comment-time pull-right text-muted">发表于<?php echo date('Y-m-d H:i:s', $floorList[$floorId]['add_time'])?></span>
            </h5>
        </div>
       
        <div class="col-md-2 comment-avatar">
            <a href="#"><img src="/application/assets/images/avatar.jpg" class="img-responsive"/></a>
        </div>
        <div class="col-md-10 comment-body">
            <div class="comment-quote-wrap">
                <?php echo !empty($building['floors']) ? $this->renderQuote2($building['floors'], $floorList) : ''?>
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
    <?php endForeach?>
    
    <?php endIF?>
    </div>
    <hr/>
   <nav class="text-center">
    <?php echo $pagination?>
   </nav>
    
  	<div class="row comment-add">
    	<div class="col-md-offset-3 col-md-6">
    		<textarea maxLength="200"></textarea>
    	</div>
    	<div class="col-md-offset-3 col-md-6 text-center" style="position: relative">
    		<button class="btn btn-primary" id="add-building">提交评论</button>
    	</div>
    </div>
    
    <input type="hidden" id="torrentId" value="<?php echo $torrentInfo['id']?>">
    <button class="btn btn-default comment-tip">+ 1</button>
    <script src="<?php echo framework\App::ins()->request->getBaseUrl()?>application/assets/js/comment.js"></script>