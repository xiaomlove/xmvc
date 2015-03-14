<?php if ($isFirst):?>
<div class="col-md-offset-2 col-md-10 bg-warning text-danger forum-thread-support-title"><h4><span class="glyphicon glyphicon-record" aria-hidden="true"></span>支持</h4></div>
<div class="col-md-offset-2 col-md-10">
		<table class="table">
			<thead>
				<tr>
					<th>参与人数<em class="text-danger bg-warning appraise-user-count">1</em></th>
					<th>魔力<em class="text-danger bg-warning appraise-bonus-count"><?php echo $data['bonus']?></em></th>
					<th>理由</th>
					<th style="text-align: right"><a href="#">收起</a></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href="#"><?php echo $userName?></a></td>
					<td><?php echo $data['bonus']?></td>
					<td colspan="2"><?php echo $data['reason']?></td>
				</tr>
				
			</tbody>
		</table>
</div>
<?php else:?>
<tr>
	<td><a href="#"><?php echo $userName?></a></td>
	<td><?php echo $data['bonus']?></td>
	<td colspan="2"><?php echo $data['reason']?></td>
</tr>
<?php endIf?>