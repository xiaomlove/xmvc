<form class="form-horizontal" action="" method="post">
  <div class="form-group <?php echo $model->hasError('title') ? "has-error" : ""?>">
    <label for="title" class="col-sm-2 control-label">标题</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="title" name="title" value="<?php echo $model->getData('title')?>" placeholder="主题标题">
        <?php if($model->hasError('title')):?>
        <span class="help-block"><?php echo $model->getError('title')?></span>
        <?php endIf?>
    </div>
  </div>
  <div class="form-group <?php echo $model->hasError('content') ? "has-error" : ""?>">
    <label for="content" class="col-sm-2 control-label">内容</label>
    <div class="col-sm-10">
        <script id="uecontainer" name="content" type="text/plain"></script>
        <?php if($model->hasError('content')):?>
        <span class="help-block"><?php echo $model->getError('content')?></span>
        <?php endIf?>
     </div>
  </div>
  
  <input type="hidden" name="section_id" value="<?php echo $_GET['section_id']?>">
  <?php if ($model->hasData('thread_id')):?>
  <input type="hidden" name="thread_id" value="<?php echo $model->getData('thread_id')?>">
  <?php endIf?>
  <?php if ($model->hasData('state')):?>
  <input type="hidden" name="state" value="<?php echo $model->getData('state')?>">
  <?php endIf?>
  <div class="form-group">
    <div class="col-sm-offset-6 col-sm-6">
      <button type="submit" class="btn btn-success">发表</button>
      <button type="button" id="save-draft" class="btn btn-default pull-right<?php if ($model->getData('state') == 1) echo ' disabled'?>">保存草稿</button>
    </div>
  </div>
</form>
<input type="hidden" id="draft-thread-id" value="">
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.config2.js"></script>
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.all.min.js"></script>
  <script type="text/javascript">
  var ue = UE.getEditor('uecontainer');
  ue.ready(function() {
      ue.setContent('<?php echo $model->getData('content')?>');
      
  });

  //保存手稿
	$saveBtn = $("#save-draft");
	if (!$saveBtn.hasClass("disabled")){
		$saveBtn.on("mousedownss", function(e){
			var content = ue.getContent();
			if ($.trim(content) == ""){
				alert("请填写内容");
				return;
			}
			var title = $("#title").val();
			if ($.trim(title) == ""){
				alert("请填写内容");
				return;
			}
			var url = document.URL;
			var thread_id = "";
			if (url.indexOf("thread_id=") == -1){
				thread_id = $("form").find("input[name=thread_id]").val();
				if(!thread_id){
					thread_id = $("#draft-thread-id").val();
				}
				if (thread_id){
					thread_id = "&thread_id="+thread_id;
				}
			}
			$.ajax({
				url: url,
				type: "POST",
				dataType: "json",
				beforeSend: function(){$saveBtn.text("保存中...").attr("disabled", "disabled")},
				data: "title="+encodeURIComponent(title)+"&content="+encodeURIComponent(content)+"&draft=1"+thread_id,
				success: function(data){
					console.log(data);
				}
			})
			
		})
	}
  
  </script>