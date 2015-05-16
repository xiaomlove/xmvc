<h3 class="main-title">
	<strong>为角色分配权限</strong>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/role/rolelist')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>返回列表</a>
</h3>
<div class="row">
	<div class="col-md-offset-2 col-md-8 role-add-rule">
	<?php if (!empty($ruleList)):?>
	<?php foreach ($ruleList as $rule):?>
		<div><label><span><?php echo str_repeat('----', $rule['level']-1)?></span><input type="checkbox"<?php if ($rule['checked']) echo "checked"?> data-id="<?php echo $rule['id']?>" data-path="<?php echo $rule['path']?>" data-level="<?php echo $rule['level']?>" data-parent="<?php echo $rule['parent_id']?>"><?php echo $rule['name']?></label></div>
	<?php endForeach?>
		<div style="margin-top: 20px">
			<button class="btn btn-primary" id="submit" data-container="body" data-toggle="popover" data-placement="right" data-content="">保存</button>
		</div>
	<?php endIf?>	
	</div>
</div>
<input type="hidden" id="role_id" value="<?php echo $_GET['id']?>">
<input type="hidden" id="add-rule-url" value="<?php echo $this->createUrl('manage/role/addrule')?>">
<script>
	var $inputList = $("input[type=checkbox]");
	$inputList.on("click", function(e){
		if ($(this).prop("checked")){
			//被选中，父元素必须被选中
			var path = $(this).attr("data-path");
			var parentIdArr = path.split(",");
			if (parentIdArr.length){
				parentIdArr.forEach(function(parentId){
					$("input[data-id="+parentId+"]").prop("checked", true);
				})
			}
		}else{
			//不选中，子元素必须不能被选中
			var thisPath = $(this).attr("data-path");
			$inputList.each(function(index, elem){
				var path = $(this).attr("data-path");
				var re = new RegExp("^"+thisPath+",");
				if (re.test(path)){
					$(this).prop("checked", false);
				}
			})
		}
			
	});

	var $submit = $("#submit");
	$submit.click(function(e){
		var checked = [];
		$inputList.each(function(){
			if ($(this).prop("checked")){
				checked.push($(this).attr("data-id"));
			}
		});
		if (!checked.length){
			if(!confirm("没有分配任何权限，是否确定？")){
				return;
			}
		}
		var role_id = $("#role_id").val();
		var url = $("#add-rule-url").val();
		$.ajax({
			url: url,
			type: "POST",
			dataType: "json",
			data: "role_id="+role_id+"&ruleIdList="+checked.join("_"),
			success: function(data){
				//if (data.code == 1){
					$submit.attr("data-content", data.msg).popover('show');
					setTimeout(function(){$submit.popover('destroy')}, 2000);
				//}
			}
		})
	})
</script>


