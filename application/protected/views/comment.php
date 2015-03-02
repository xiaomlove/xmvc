  	<?php if($navHtml !== 0):?>
  	<nav class="comment-list-nav">
        <?php echo $navHtml?>
      </nav>
     <?php endIf?>
      <div id="comment-item">
      <?php foreach($comments as $comment):?>
      
      <div class="item">
        <h4 class="comment-head">#<span class="comment-floor"><?php echo $comment['floor']?></span><span class="text-primary comment-username"><?php echo $comment['name']?>(主管)</span></h4>
        <div class="clearfix">
          <div class="col-xs-2 avatar">
            <img src="application/public/images/avatar.jpg" class="img-responsive"/>
          </div>
          <div class="col-xs-10 comment-content"><?php echo $comment['content']?></div>
        </div>
        <div class="clearfix comment-foot">
          <div class="col-xs-2 social">
            <span class="text-danger">私信</span><span class="text-primary">加好友</span>
          </div>
          <div class="col-xs-10 action">
            <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
            <span class="text-info">评论</span>
            <span class="text-danger">举报</span>            
          </div>
        </div>
      </div>
      <?php endForeach?>
      </div>
  	<?php if($navHtml !== 0):?>
  	<nav class="comment-list-nav">
        <?php echo $navHtml?>
      </nav>
     <?php endIf?>
     <input type="hidden" value="<?php echo $total?>" id="comment-total">
