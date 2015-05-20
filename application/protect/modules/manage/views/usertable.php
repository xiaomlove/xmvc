

	<?php if (!empty($userList)):?>
	<?php foreach ($userList as $user):?>
	<tr>
		<td><?php echo $user['id']?></td>
		<td><?php echo $user['name']?></td>
		<td><?php echo substr_replace($user['email'], '*', 0, 1)?></td>
		<td><?php echo $this->getSize($user['uploaded'])?></td>
		<td><?php echo $this->getSize($user['downloaded'])?></td>
		<td><?php echo date('Y-m-d H:i', $user['last_login_time'])?></td>
		<td><?php echo $user['role_name']?></td>
		<td>
			<a href="<?php echo $this->createUrl('manage/user/detail', array('id' => $user['id']))?>">查看详情</a>
		</td>
	</tr>
	<?php endForeach?>
	<?php else:?>
	<tr><td colspan="8">暂无用户</td></tr>
	<?php endIf?>

