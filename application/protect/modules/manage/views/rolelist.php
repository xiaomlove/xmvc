<h3 class="main-title">
	<strong>【<?php echo $roleGroup['name']?>】下的&nbsp;[角色列表]</strong>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/rolegroup/list')?>"><span class="glyphicon glyphicon-arrow-left"></span>返回列表</a>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/role/roleadd')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加角色</a>
</h3>
<table class="table table-bordered section-list-table">
	<thead>
	<tr>
		<th>角色名</th>
		<th>等级</th>
		<th>魔力</th>
		<th>下载量(GB)</th>
		<th>上传量(GB)</th>
		<th>分享率</th>
		<th>注册时间</th>
		<th>操作</th>
	</tr>
	</thead>
	
	<tbody>
	<?php if (!empty($roleList)):?>
	<?php foreach ($roleList as $role):?>
	<tr data-id="<?php echo $role['id']?>">
		<td><?php echo $role['name']?></td>
		<td><?php echo $role['level']?></td>
		<td><?php echo $role['bonus_limit']?></td>
		<td><?php echo $role['downloaded_limit']?></td>
		<td><?php echo $role['uploaded_limit']?></td>
		<td><?php echo number_format($role['ratio_limit'], 2, '.', '')?></td>
		<td>
			<?php 
			echo $role['register_time_limit_value'].$this->getUnit($role['register_time_limit_unit']);
			?>
		</td>
		<td>
			<a href="<?php echo $this->createUrl('manage/role/roleedit', array('id' => $role['id']))?>">编辑</a>
			<a href="<?php echo $this->createUrl('manage/role/addrule', array('id' => $role['id']))?>">权限</a>
			<a href="#">删除</a>
		</td>
	</tr>
	<?php endforeach?>
	<?php else:?>	
	<tr><td colspan="8">没有角色，请先添加</td></tr>
	<?php endIf?>

	</tbody>
</table>
