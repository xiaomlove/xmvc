<div class="row">
      <table class="table table-bordered table-hover table-striped" id="torrent-list-table">
        <thead>
          <tr>
            <th style="text-align: center">标题<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></th>
            <th>添加于</th>
            <th title="存活时间">TTL</th>
            <th>大小</th>
            <th>做种</th>
            <th>下载</th>
            <th>完成</th>
            <th>评论</th>
            <th>查看</th>
            <th>发布者</th>
          </tr>
        </thead>
        <?php if(!empty($data)):?>
        <tbody>
        <?php foreach ($data as $k => $v):?>
          <tr>
            <td><a href="<?php echo $this->createUrl('torrent/detail', array('id' => $v['id']))?>"><?php echo $v['main_title']."<br/>".$v['slave_title']?></a></td>
            <td><?php echo date('Y-m-d', $v['add_time'])."<br/>".date('H:i:s', $v['add_time'])?></td>
            <td><?php echo $this->getTorrentTTL($v['add_time'], '<br/>')?></td>
            <td><?php echo $this->getTorrentSize($v['size'])?></td>
            <td title="做种"><?php echo $v['seeder_count']?></td>
            <td title="下载"><?php echo $v['leecher_count']?></td>
            <td title="完成"><?php echo $v['finish_times']?></td>
            <td title="评论"><?php echo $v['comment_count']?></td>
            <td title="查看"><?php echo $v['view_times']?></td>
            <td><a href="#"><?php echo $v['user_name']?></a></td>
          </tr>
          <?php endForeach?>
        </tbody>
        <?php endIf?>
      </table>
      <nav id="torrent-list-nav">
        <?php echo $navHtml?>
      </nav>
    </div>