<h3 class="main-title">
	<?php if (empty($parent)):?>
	<strong>一级版块列表</strong>
	<?php else:?>
	<strong><?php echo $parent['name']?></strong>&nbsp;&nbsp;的子版块
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/forum/sectionlist')?>"><span class="glyphicon glyphicon-arrow-left"></span>返回列表</a>
	<?php endIf?>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/forum/sectionadd')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>新增版块</a>
</h3>
<table class="table table-bordered section-list-table">
	<thead>
	<tr>
		<th>名称</th>
		<th>排序</th>
		<th>查看</th>
		<th>回复</th>
		<th>发表</th>
		<th>帖子数量</th>
		<th>版主</th>
		<th>操作</th>
	</tr>
	</thead>
	
	<tbody>
	<?php if (!empty($sectionList)):?>
	<?php foreach ($sectionList as $section):?>
	<tr>
		<td><?php echo $section['name']?></td>
		<td><?php echo $section['sort']?></td>
		<td><?php echo $section['view_limit_name'] ? $section['view_limit_name'] : "无限制"?></td>
		<td><?php echo $section['reply_limit_name'] ? $section['reply_limit_name'] : "无限制"?></td>
		<td><?php echo $section['publish_limit_name'] ? $section['publish_limit_name'] : "无限制"?></td>
		<td><?php echo $section['thread_total_count']?></td>
		<td><?php echo $section['master_name_list']?></td>
		<td>
		<?php if (empty($parent)):?>
			<a href="<?php echo $this->createUrl('manage/forum/sectionlist', array('parent_id' => $section['id']))?>">查看子版块</a>
		<?php endIf?>
			<a href="<?php echo $this->createUrl('manage/forum/sectionedit', array('id' => $section['id']))?>">编辑</a>
			<a href="#">删除</a>
		</td>
	</tr>
	<?php endForeach?>
	<?php else:?>
	
	<tr><td colspan="8">暂无版块，请先添加</td></tr>
	<?php endIf?>
	
	
	
	</tbody>
</table>
