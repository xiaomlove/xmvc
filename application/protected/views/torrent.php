<div class="row">
      <table class="table table-bordered table-hover table-striped" id="torrent-list-table">
        <thead>
          <tr>
            <th style="text-align: center"><?php echo $this->getSortHref('main_title', '标题')?></th>
            <th><?php echo $this->getSortHref('add_time', '添加于')?></th>
            <th title="存活时间">TTL</th>
            <th><?php echo $this->getSortHref('size', '大小')?></th>
            <th><?php echo $this->getSortHref('seeder_count', '做种')?></th>
            <th><?php echo $this->getSortHref('leecher_count', '下载')?></th>
            <th><?php echo $this->getSortHref('finish_times', '完成')?></th>
            <th><?php echo $this->getSortHref('comment_count', '评论')?></th>
            <th><?php echo $this->getSortHref('view_times', '查看')?></th>
            <th><?php echo $this->getSortHref('user_name', '发布者')?></th>
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