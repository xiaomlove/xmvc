<style>
	td span{margin-right: 10px;cursor: pointer}
	.modal-title,.modal-body,.modal-footer{text-align: center}
	#add-error,#field-error{display: none}
	.modal-label{margin-bottom: 10px}
</style>
<h3 class="main-title">
	<strong>种子分类项</strong>
	<a id="add-btn" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加分类项</a>
</h3>

<table class="table table-bordered table-hover role-group-table">
	<thead>
		<tr>
			<th>分类项ID</th>
			<th>分类项名称</th>
			<th>torrent字段</th>
			<th>排序</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody id="tbody">
	<?php if (!empty($categoryList)):?>
	<?php foreach ($categoryList as $category):?>
		<tr id="<?php echo $category['id']?>">
			<td><?php echo $category['id']?></td>
			<td class="category-name"><?php echo $category['name']?></td>
			<td class="torrent-field"><?php echo $category['value']?></td>
			<td>
				<span class="glyphicon move glyphicon-arrow-up" aria-hidden="true" title="上移"></span>
				<span class="glyphicon move glyphicon-arrow-down" aria-hidden="true" title="下移"></span>
			</td>
			<td>
				<a href="javascript:;" class="btn btn-info btn-xs change-btn">修改</a>
				<a href="<?php echo $this->createUrl('manage/category/sublist', array('parent_id' => $category['id']))?>">查看子项目</a>
				<a href="javascript:;">删除</a>
			</td>
		</tr>
	<?php endforeach?>
	<?php else:?>
		<tr><td colspan="4">暂无分类项</td></tr>
	<?php endIf?>
	</tbody>
</table>

<div id="add-modal" class="modal fade">
  <div class="modal-dialog modal-sm">
    <div class="modal-content" style="top: 200px">
      <div class="modal-header">
        <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">新建分类项</h4>
      </div>
      <div class="modal-body">
        <div class="modal-label">
        	<div>分类项名称：<input type="text" id="add-input" data-id="false"></div>
        	<div><span id="add-error" class="text-danger">不能为空！</span></div>
        </div>
        <div class="modal-label">
        	<div>torrent字段：<input type="text" id="field-input"></div>
        	<div><span id="field-error" class="text-danger">不能为空！</span></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default modal-cancel">取消</button>
        <button type="button" class="btn btn-primary modal-submit">确定</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
	//添加父分类项
	var $addBtn = $('#add-btn');
	var $modal = $('#add-modal');
	var $modalTitle = $modal.find('.modal-title');
	var $addInput = $('#add-input');
	var $fieldInput = $('#field-input');
	var $addSubmit = $modal.find('.modal-submit');
	var $addError = $('#add-error');
	var $fieldError = $('#field-error');
	var $addClose = $modal.find('.close');
	var $addCancel = $modal.find('.modal-cancel');
	var creating = false;

	var addUrl = '<?php echo $this->createUrl('manage/category/addparent')?>';
	var editUrl = '<?php echo $this->createUrl('manage/category/editparent')?>';
	
	$addBtn.click(function(e) {
		$modalTitle.text('添加分类项');
		$modal.modal({
			backdrop: 'static',
			keyboard: false,
		});
	});

	$addClose.add($addCancel).on("click", function(e) {
		if (!creating) {
			$modal.modal('hide');
		}
	});
	
	$modal.on('shown.bs.modal', function(e) {
		$addInput.focus();
	});

	$modal.on('hidden.bs.modal', function(e) {
		$addError.hide();
		$fieldError.hide();
		$addInput.val('').attr('data-id', 'false');
		$fieldInput.val('');
		$addSubmit.text('确定');
	});

	$addSubmit.click(function(e) {
		var value = $addInput.val().trim();
		var field = $fieldInput.val().trim();
		var id = $addInput.attr('data-id');
		if (value === '') {
			$addError.text('不能为空！').show();
			$addInput.focus();
			return;
		};
		if (field === '') {
			$fieldError.text('不能为空！').show();
			$fieldInput.focus();
			return;
		}
		if (typeof id !== 'undefined' && id !== 'false') {
			var url = editUrl + '?id=' + id;
		} else {
			var url = addUrl;
		}
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			beforeSend: function() {
				$addSubmit.text('请稍后...').attr('disabled', true);
				creating = true;
			},
			data: 'name=' + encodeURIComponent(value) + '&field=' + encodeURIComponent(field),
			timeout: 8000,
		}).done(function(result) {
// 			console.log(result);return;
			if (result.code == 1 || result.code == 0) {
				$modal.modal('hide');
				window.location.reload();
				return;
			} else if (result.code == -2 || result.code == -4) {
				$fieldError.text(result.msg).show();
			} else if (result.code == -3) {
				$addError.text(result.msg).show();
			}
			creating = false;
			$addSubmit.text(result.msg).removeAttr('disabled');
		}).error(function(xhr, errorText, errorThrow) {
			creating = false;
			$addSubmit.text(errorText).removeAttr('disabled');
		})
			
	});

	//修改名称
	$('.change-btn').on('click', function(e) {
		var $tr = $(this).parent().parent();
		var name = $tr.find('.category-name').text();
		var field = $tr.find('.torrent-field').text();
		var id = $tr.attr('id');
		$addInput.val(name).attr('data-id', id);
		$fieldInput.val(field);
		$modalTitle.text('修改分类项');
		$modal.modal({
			backdrop: 'static',
			keyboard: false,
		});
	});

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

	
</script>