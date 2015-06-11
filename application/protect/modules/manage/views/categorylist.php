<style>
	td span{margin-right: 10px;cursor: pointer}
	.modal-title,.modal-body,.modal-footer{text-align: center}
	#add-error{display: none}
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

<div id="add-modal" class="modal fade">
  <div class="modal-dialog modal-sm">
    <div class="modal-content" style="top: 200px">
      <div class="modal-header">
        <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">新建分类项</h4>
      </div>
      <div class="modal-body">
        <p>分类项名称：<input type="text" id="add-input"></p>
        <span id="add-error" class="text-danger">不能为空！</span>
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
	var $modalContent = $modal.find('.modal-content');
	var $addInput = $('#add-input');
	var $addSubmit = $modal.find('.modal-submit');
	var $addError = $('#add-error');
	var $addClose = $modal.find('.close');
	var $addCancel = $modal.find('.modal-cancel');
	var creating = false;
	
	$addBtn.click(function(e) {
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
	});

	$addSubmit.click(function(e) {
		var value = $addInput.val().trim();
		if (value === '') {
			$addError.show();
			$addInput.focus();
			return;
		}
		$.ajax({
			url: '<?php echo $this->createUrl('manage/category/addparent')?>',
			type: 'POST',
			dataType: 'json',
			beforeSend: function() {
				$addSubmit.text('请稍后...').attr('disabled', true);
				creating = true;
			},
			data: 'name=' + encodeURIComponent(value),
			timeout: 8000,
		}).done(function(result) {
//			console.log(result);
			if (result.code == 1) {
				$modal.modal('hide');
				window.location.reload();
			} else {
				creating = false;
				$addSubmit.text(result.msg).removeAttr('disabled');
				
			}
		}).error(function(xhr, errorText, errorThrow) {
			creating = false;
			$addSubmit.text(errorText).removeAttr('disabled');
		})
			
	})
	
</script>