<style>
	td span{margin-right: 10px;cursor: pointer}
</style>
<h3 class="main-title">
	<strong>种子分类项</strong>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/category/add')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加分类项</a>
</h3>

<table class="table table-bordered table-hover role-group-table">
	<thead>
		<tr>
			<th>分类项ID</th>
			<th>分类项名称</th>
			<th>排序</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
	<?php if (!empty($categoryList)):?>
	<?php foreach ($categoryList as $category):?>
		<tr>
			<td><?php echo $category['id']?></td>
			<td><?php echo $category['name']?></td>
			<td>
				<span class="glyphicon glyphicon-arrow-up" aria-hidden="true" title="上移"></span>
				<span class="glyphicon glyphicon-arrow-down" aria-hidden="true" title="下移"></span>
			</td>
			<td>
				<a href="javascript:;" class="btn btn-info btn-xs">修改名称</a>
				<a href="<?php echo $this->createUrl('manage/category/list', array('parent_id' => $category['id']))?>">查看子项目</a>
				<a href="javascript:;">删除</a>
			</td>
		</tr>
	<?php endforeach?>
	<?php else:?>
		<tr><td colspan="4">暂无分类项</td></tr>
	<?php endIf?>
	</tbody>
</table>