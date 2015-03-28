<div class="row">	
	<h2 class="page-header" style="text-align: center">
		<strong style="display: block;margin: 10px">种子完成情况：</strong>
		<a href="<?php echo $this->createUrl('torrent/detail', array('id' => $torrentInfo['id']))?>"><?php echo $this->getTorrentName($torrentInfo['name'])?></a>
		<small style="display: block;margin-top: 20px">(越靠前越后面完成)</small>
	</h2>
	<table class="table table-hover table-bordered">
		<thead>
			<tr>
				<th>用户名</th>
				<th>上传量</th>
				<th>即时速度</th>
				<th>分享率</th>
				<th>下载时间</th>
				<th>下载速度</th>
				<th>完成时间</th>
				<th>做种时间</th>
				<th>最近汇报</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		<?php if (!empty($snatchList)):?>
		<?php foreach ($snatchList as $snatch):?>
			<tr>
				<td><?php echo $snatch['user_name']?></td>
				<td><?php echo $this->getSize($snatch['uploaded'])?></td>
				<td><?php echo $this->getSpeed($snatch['upload_speed'])?></td>
				<td><?php echo number_format($snatch['uploaded']/$snatch['torrent_size'], 3)?></td>
				<td><?php echo $snatch['complete_time'] - $snatch['start_time']?></td>
				<td><?php echo $this->getSpeed($snatch['torrent_size']/($snatch['complete_time'] - $snatch['start_time']))?></td>
				<td><?php echo date('Y-m-d H:i', $snatch['complete_time'])?></td>
				<td><?php echo $snatch['connect_time']?></td>
				<td><?php echo $snatch['this_report_time']?></td>
				<td><a href="javascript:;">举报</a></td>
			</tr>
		<?php endforeach?>
		<?php endIf?>
		</tbody>
	</table>
</div>
