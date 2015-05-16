<h3 class="main-title">
	<strong>用户组列表</strong>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/rolegroup/add')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加用户组</a>
</h3>

<table class="table table-bordered role-group-table">
	<thead>
		<tr>
			<th>用户组ID</th>
			<th>名称</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
	<?php if (!empty($roleGroupList)):?>
	<?php foreach ($roleGroupList as $ruleGroup):?>
		<tr>
			<td><?php echo $ruleGroup['id']?></td>
			<td><?php echo $ruleGroup['name']?></td>
			<td>
				<a href="<?php echo $this->createUrl('manage/rolegroup/edit', array('id' => $ruleGroup['id']))?>">编辑</a>
				<a href="<?php echo $this->createUrl('manage/role/rolelist', array('group_id' => $ruleGroup['id']))?>">角色等级</a>
				<a href="javascript:;">删除</a>
			</td>
		</tr>
	<?php endforeach?>
	<?php else:?>
		<tr><td colspan="3">暂无用户组</td></tr>
	<?php endIf?>
	</tbody>
</table>