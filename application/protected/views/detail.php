<div class="row torrent-detail">
	<?php if (!empty($torrent)):?>
      <h1 class="torrent-title"><?php echo $torrent['main_title']?></h1>
      <?php if(App::ins()->user->hasFlash('upload_success')):?>
      <h1 class="torrent-title text-danger"><?php echo App::ins()->user->getFlash('upload_success')?></h1>
      <?php endIf?>
      <table class="table table-bordered" id="detail-table">

        <tbody>
          <tr>
            <td>下载</td>
            <td><a href="download?id=<?php echo $torrent['id']?>"><?php echo $this->getTorrentName($torrent['name'])?></a></td>
          </tr>
          <tr>
            <td>副标题</td>
            <td><?php echo $torrent['slave_title']?></td>
          </tr>
          <tr>
            <td>基本信息</td>
            <td>大小：<span class="text-success"><?php echo $this->getTorrentSize($torrent['size'])?></span>类型：<span class="text-success">Movie</span></td>
          </tr>
          <tr>
            <td>行为</td>
            <td><a href="#" class="text-danger">删除种子</a><a href="#" class="text-primary">编辑种子</a><a href="#" class="text-warning">举报种子</a></td>
          </tr>
          <tr>
            <td>简介</td>
            <td>
              <div id="introduce"><?php echo $torrent['introduce']?></div>
            </td>
          </tr>
          <tr>
            <td>种子信息</td>
            <td>hash码：<span class="text-primary"><?php echo $torrent['info_hash']?></span></td>
          </tr>
          <tr>
            <td>热度表</td>
            <td>查看：<span class="text-primary"><?php echo $torrent['view_times']?>次</span>下载：<span class="text-primary"><?php echo $torrent['download_times']?>次</span>完成：<span class="text-primary"><?php echo $torrent['finish_times']?>次</span></td>
          </tr>
          <tr>
            <td>同伴</td>
            <td><button class="btn btn-xs btn-info">查看小伙伴们</button>做种者：<span class="text-primary"><?php echo $torrent['seeder_count']?>个</span>下载者：<span class="text-primary"><?php echo $torrent['leecher_count']?>个</span></td>
          </tr>
          <tr>
            <td>感谢者</td>
            <td><button class="btn btn-success btn-xs">说谢谢</button><span class="text-primary">admin1</span><span class="text-primary">admin2</span><span class="text-primary">xiaomiao</span></td>
          </tr>
        </tbody>
      </table>
      <h3 class="torrent-title">用户评论</h3>
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
      <div class="item">
        <h4 class="comment-head">#1<span class="text-primary">admin(主管)</span></h4>
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
      <div class="item">
        <h4 class="comment-head">#2<span class="text-primary">admin(主管)</span></h4>
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
      <div class="item">
        <h4 class="comment-head">#3<span class="text-primary">admin(主管)</span></h4>
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
      <div class="item">
        <h4 class="comment-head">#4<span class="text-primary">admin(主管)</span></h4>
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
      <h3 class="torrent-title">添加评论</h3>
      <form class="form-horizontal" role="form" id="upload-form">    
        <div class="form-group">
          
          <div class="col-sm-offset-3 col-sm-6">
            <textarea class="form-control" rows="4"></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6 submit-btn">
            <div id="expression">
              <img src="application/public/images/QQexpression/1.gif">
              <img src="application/public/images/QQexpression/2.gif">
              <img src="application/public/images/QQexpression/3.gif">
              <img src="application/public/images/QQexpression/4.gif">
              <img src="application/public/images/QQexpression/5.gif">
              <img src="application/public/images/QQexpression/6.gif">
              <img src="application/public/images/QQexpression/7.gif">
              <img src="application/public/images/QQexpression/8.gif">
              <img src="application/public/images/QQexpression/9.gif">
              <img src="application/public/images/QQexpression/10.gif">
              <img src="application/public/images/QQexpression/11.gif">
              <img src="application/public/images/QQexpression/12.gif">
              <img src="application/public/images/QQexpression/13.gif">
              <img src="application/public/images/QQexpression/14.gif">
              <img src="application/public/images/QQexpression/15.gif">
              <img src="application/public/images/QQexpression/16.gif">
            </div>
            <button type="submit" class="btn btn-primary" id="submit" disabled>添加</button>
            
          </div>
        </div>
      </form>
      <?php else:?>
      <h1 class="torrent-title">西淳记之大对娶亲</h1>
      <?php if(App::ins()->user->hasFlash('upload_success')):?>
      <h1 class="torrent-title text-danger"><?php echo App::ins()->user->getFlash('upload_success')?></h1>
      <?php endIf?>
      <table class="table table-bordered" id="detail-table">

        <tbody>
          <tr>
            <td>下载</td>
            <td>西淳记之大对娶亲</td>
          </tr>
          <tr>
            <td>副标题</td>
            <td>西淳记之大对娶亲</td>
          </tr>
          <tr>
            <td>基本信息</td>
            <td>大小：<span class="text-success">4GB</span>类型：<span class="text-success">Movie</span></td>
          </tr>
          <tr>
            <td>行为</td>
            <td><a href="#" class="text-danger">删除种子</a><a href="#" class="text-primary">编辑种子</a><a href="#" class="text-warning">举报种子</a></td>
          </tr>
          <tr>
            <td>简介</td>
            <td>
              <div><img src="application/public/images/1.jpg" style="width:800px;margin-bottom:30px"/></div>
              大闹天宫后四百年多年，齐天大圣成了一个传说，在山妖横行的长安城，孤儿江流儿与行脚僧法明相依为命，小小少年常常神往大闹天宫的孙悟空。有一天，山妖来劫掠童男童女，江流儿救了一个小女孩，惹得山妖追杀，他一路逃跑，跑进了五行山，盲打误撞地解除了孙悟空的封印。悟空自由之后只想回花果山，却无奈腕上封印未解，又欠江流儿人情，勉强地护送他回长安城。一路上八戒和白龙马也因缘际化地现身，但或落魄或魔性大发，英雄不再。妖王为抢女童，布下夜店迷局，却发现悟空法力尽失，轻而易举地抓走了女童。悟空不愿再去救女童，江流儿决定自己去救。日全食之日，在悬空寺，妖王准备将童男童女投入丹炉中，江流儿却冲进了道场，最后一战开始了……

            </td>
          </tr>
          <tr>
            <td>种子信息</td>
            <td>hash码：<span class="text-primary">3b6180bbdc253f538bc5c41dbbd04309438535ad</span></td>
          </tr>
          <tr>
            <td>热度表</td>
            <td>查看：<span class="text-primary">12次</span>下载：<span class="text-primary">12次</span>完成：<span class="text-primary">12次</span></td>
          </tr>
          <tr>
            <td>同伴</td>
            <td><button class="btn btn-xs btn-info">查看小伙伴们</button>做种者：<span class="text-primary">1个</span>下载者：<span class="text-primary">2个</span></td>
          </tr>
          <tr>
            <td>感谢者</td>
            <td><button class="btn btn-success btn-xs">说谢谢</button><span class="text-primary">admin1</span><span class="text-primary">admin2</span><span class="text-primary">xiaomiao</span></td>
          </tr>
        </tbody>
      </table>
      <h3 class="torrent-title">用户评论</h3>
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
      <div class="item">
        <h4 class="comment-head">#1<span class="text-primary">admin(主管)</span></h4>
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
      <div class="item">
        <h4 class="comment-head">#2<span class="text-primary">admin(主管)</span></h4>
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
      <div class="item">
        <h4 class="comment-head">#3<span class="text-primary">admin(主管)</span></h4>
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
      <div class="item">
        <h4 class="comment-head">#4<span class="text-primary">admin(主管)</span></h4>
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
      <h3 class="torrent-title">添加评论</h3>
      <form class="form-horizontal" role="form" id="upload-form">    
        <div class="form-group">
          
          <div class="col-sm-offset-3 col-sm-6">
            <textarea class="form-control" rows="4"></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6 submit-btn">
            <div id="expression">
              <img src="application/public/images/QQexpression/1.gif">
              <img src="application/public/images/QQexpression/2.gif">
              <img src="application/public/images/QQexpression/3.gif">
              <img src="application/public/images/QQexpression/4.gif">
              <img src="application/public/images/QQexpression/5.gif">
              <img src="application/public/images/QQexpression/6.gif">
              <img src="application/public/images/QQexpression/7.gif">
              <img src="application/public/images/QQexpression/8.gif">
              <img src="application/public/images/QQexpression/9.gif">
              <img src="application/public/images/QQexpression/10.gif">
              <img src="application/public/images/QQexpression/11.gif">
              <img src="application/public/images/QQexpression/12.gif">
              <img src="application/public/images/QQexpression/13.gif">
              <img src="application/public/images/QQexpression/14.gif">
              <img src="application/public/images/QQexpression/15.gif">
              <img src="application/public/images/QQexpression/16.gif">
            </div>
            <button type="submit" class="btn btn-primary" id="submit" disabled>添加</button>
            
          </div>
        </div>
      </form>
      <?php endIf?>
    </div>
 <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.parse.min.js"></script>
 <script>
 uParse('#introduce', {
	    rootPath: './'
	})

 </script>