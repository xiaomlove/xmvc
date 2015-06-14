<style>
	td .glyphicon{margin-right: 10px;cursor: pointer}
	.modal-title,.modal-body,.modal-footer{text-align: center}
	#add-error,#field-error{display: none}
	.modal-label{margin-bottom: 10px}
</style>
<h3 class="main-title">
	<strong>【<?php echo $parent['name']?>】下的具体分类项目</strong>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/category/parentlist')?>"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>返回</a>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/category/addsub', array('parent_id' => $parent['id']))?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加</a>
</h3>

<table class="table table-bordered table-hover sub-category-table">
	<thead>
		<tr>
			<th>名称</th>
			<th>图标</th>
			<th>torrent值</th>
			<th>排序</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody id="tbody">
	<?php if (!empty($subCategoryList)):?>
	<?php foreach ($subCategoryList as $category):?>
		<tr id="<?php echo $category['id']?>">
			<td><?php echo $category['name']?></td>
			<td><span class="category-icon" title="点击更换" style="cursor: pointer;background-image: url('<?php echo empty($category['icon_src']) ? '/application/assets/images/catsprites.png' : $category['icon_src']?>')"></span></td>
			<td><?php echo $category['value']?></td>
			<td>
				<span class="glyphicon move glyphicon-arrow-up" aria-hidden="true" title="上移"></span>
				<span class="glyphicon move glyphicon-arrow-down" aria-hidden="true" title="下移"></span>
			</td>
			<td>
				<a href="<?php echo $this->createUrl('/manage/category/editsub', array('id' => $category['id']))?>">编辑</a>
				<a href="javascript:;">删除</a>
			</td>
		</tr>
	<?php endforeach?>
	<?php else:?>
		<tr><td colspan="5">暂无具体项目</td></tr>
	<?php endIf?>
	</tbody>
</table>
<link rel="stylesheet" href="<?php echo framework\App::ins()->request->getBaseUrl()?>application/assets/lib/xmcrop/css/jquery.Jcrop.css"></link>
<script src="<?php echo framework\App::ins()->request->getBaseUrl()?>application/assets/lib/xmcrop/jquery.Jcrop.min.js"></script>
<script src="<?php echo framework\App::ins()->request->getBaseUrl()?>application/assets/lib/xmcrop/xmcrop.js"></script>
<script>

	//上下移动
	var $tbody = $('#tbody');
	const len = $tbody.children().length;
	$tbody.on('click', '.move', function(e) {
		var $icon = $(this);
		var $tr = $icon.parents('tr[id]');
		var index = $tr.index();
		if ($icon.hasClass('glyphicon-arrow-up')) {
			if (index == 0) {
				console.log('顶端：',index);
				return;
			}
			var direction = 'up';
			var id = $icon.parents('tr[id]').attr('id');
			var $target = $tr.prev();
			var targetId = $target.attr('id');
		} else if ($icon.hasClass('glyphicon-arrow-down')) {
			if (index == len - 1) {
				console.log('底端：',index);
				return;
			}
			var direction = 'down';
			var id = $icon.parents('tr[id]').attr('id');
			var $target = $tr.next();
			var targetId = $target.attr('id');
		} else {
			$.error('没有正确的class类名');
			return;
		}
		$.ajax({
			url: '<?php echo $this->createUrl('manage/category/exchangesn')?>',
			type: 'POST',
			dataType: 'json',
			data: 'id=' + id + '&targetId=' + targetId + '&direction=' + direction,
		}).done(function(result) {
			console.log(result);
			if (result.code == 1) {
				if (direction === 'up') {
					$tr.insertBefore($target);
				} else if (direction === 'down') {
					$tr.insertAfter($target);
				} else {
					$.error('direction错误');
				}
			} else {
				$.error(result.msg);
			}
		}).error(function(xhr, errorText, errorThrow) {
			$.error(errorText);
		})
		
	});

	//修改图标
	
</script>