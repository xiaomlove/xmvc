<h3 class="main-title">
	<strong>权限列表</strong>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/rule/add')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加权限</a>
</h3>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>权限名称</th>
			<th>权限key</th>
			<th>权限mvc</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
	<?php if (!empty($ruleList)):?>
	<?php foreach ($ruleList as $rule):?>
		<tr>
			<td><?php echo str_repeat("----", $rule['level']-1).$rule['name']?></td>
			<td><?php echo $rule['rule_key']?></td>
			<td><?php echo $rule['rule_mvc']?></td>
			<td>
				<a href="<?php echo $this->createUrl('manage/rule/edit', array('id' => $rule['id']))?>">编辑</a>
				<a href="javascript:;">删除</a>
			</td>
		</tr>
	<?php endforeach?>
	<?php else:?>
		<tr><td colspan="4">暂无权限</td></tr>
	<?php endIf?>
	</tbody>
</table>