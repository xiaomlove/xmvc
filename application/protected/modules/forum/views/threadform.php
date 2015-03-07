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
  <div class="form-group">
    <div class="col-sm-offset-6 col-sm-6">
      <button type="submit" class="btn btn-success">发表</button>
      <button type="button" class="btn btn-default pull-right">保存草稿</button>
    </div>
  </div>
</form>

  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.config2.js"></script>
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.all.min.js"></script>
  <script type="text/javascript">
  var ue = UE.getEditor('uecontainer');
  ue.ready(function() {
      ue.setContent('<?php echo $model->getData('content')?>');
      //获取html内容，返回: <p>hello</p>
      var html = ue.getContent();
      //获取纯文本内容，返回: hello
      var txt = ue.getContentTxt();
  });
  </script>