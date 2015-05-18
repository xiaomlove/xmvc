
<table class="table table-bordered section-list-table">
	<thead>
	<tr>
		<th>用户名</th>
		<th>邮箱</th>
		<th>角色</th>
		<th>上传量</th>
		<th>下载量</th>
		<th>上次登陆</th>
		<th>操作</th>
	</tr>
	</thead>
	
	<tbody>
	<?php if (!empty($userList)):?>
	<?php foreach ($userList as $user):?>
	<tr>
		<td><?php echo $user['name']?></td>
		<td><?php echo substr_replace($user['email'], '*', 0, 1)?></td>
		<td><?php echo $user['role_name']?></td>
		<td><?php echo $this->getSize($user['uploaded'])?></td>
		<td><?php echo $this->getSize($user['downloaded'])?></td>
		<td><?php echo date('Y-m-d H:i', $user['last_login_time'])?></td>
		<td>
			<a href="#">查看详情</a>
			<a href="#">重置密码</a>
			<a href="#">禁用</a>
			<a href="#">删除</a>
		</td>
	</tr>
	<?php endForeach?>
	<?php else:?>
	<tr><td colspan="7">暂无用户</td></tr>
	<?php endIf?>

	</tbody>
</table>
<nav style="text-align:center">
  <?php echo $pagination?>
</nav>