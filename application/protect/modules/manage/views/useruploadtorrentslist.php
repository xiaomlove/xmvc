
<?php if (!empty($torrentList)):?>
<table class="table table-bordered table-hover">
		<thead>
          <tr>
            <th style="text-align: center">标题</th>
            <th>添加于</th>
            <th title="存活时间">TTL</th>
            <th>大小</th>
            <th>做种</th>
            <th>下载</th>
            <th>完成</th>
            <th>评论</th>
            <th>查看</th>
          </tr>
        </thead>
		<tbody>
		<?php foreach ($torrentList as $v):?>
          <tr>
            <td><a href="<?php echo $this->createUrl('torrent/detail', array('id' => $v['id']))?>"><?php echo $v['main_title']."<br/>".$v['slave_title']?></a></td>
            <td><?php echo date('Y-m-d', $v['add_time'])."<br/>".date('H:i:s', $v['add_time'])?></td>
            <td><?php echo $this->getTTL($v['add_time'], '<br/>')?></td>
            <td><?php echo $this->getSize($v['size'])?></td>
            <td title="做种"><?php echo $v['seeder_count']?></td>
            <td title="下载"><?php echo $v['leecher_count']?></td>
            <td title="完成"><?php echo $v['finish_times']?></td>
            <td title="评论"><?php echo $v['comment_count']?></td>
            <td title="查看"><?php echo $v['view_times']?></td>
          </tr>
          <?php endForeach?>
		</tbody>
</table>
<?php echo $pagination?>
<?php else:?>
	<strong>暂无种子</strong>
<?php endIf?>