<nav id="torrent-list-nav">
        <ul class="pagination">
          <li><a href="#"><span aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span></a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">4</a></li>
          <li><a href="#">5</a></li>
          <li><a href="#"><span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span></a></li>
        </ul>
      </nav>
      
      <?php foreach($comments as $comment):?>
      <div class="item">
        <h4 class="comment-head">#<span class="comment-floor">1</span><span class="text-primary comment-username">admin(主管)</span></h4>
        <div class="clearfix">
          <div class="col-xs-2 avatar">
            <img src="application/public/images/avatar.jpg" class="img-responsive"/>
          </div>
          <div class="col-xs-10 comment-content">评论内容，实在是太屌了</div>
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
      <nav id="torrent-list-nav">
        <ul class="pagination">
          <li><a href="#"><span aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span></a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">4</a></li>
          <li><a href="#">5</a></li>
          <li><a href="#"><span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span></a></li>
        </ul>
      </nav>