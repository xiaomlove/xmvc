<div class="row">
      <form class="form-horizontal" role="form" id="upload-form" method="post" action="upload.html" enctype="multipart/form-data">
        <div class="form-group">
          <div class="col-sm-12">
             <h3 class="text-danger tracker-address">服务器tracker地址是：xxxx</h3>
          </div>
        </div>
        <div class="form-group <?php echo $model->getError('torrentFile') != NULL ? "has-error" : ""?>">
          <label for="torrent-file" class="col-sm-2 control-label">种子</label>
          <div class="col-sm-10">
            <input type="file" class="form-control" id="torrent-file" name="torrentFile">
            <?php if($model->getError('torrentFile') != NULL):?>
            <span class="help-block"><?php echo $model->getError('torrentFile')?></span>
            <?php endIf?>
          </div>
        </div>
        <div class="form-group <?php echo $model->getError('main_title')!= NULL ? "has-error" : ""?>">
          <label for="mainTitle" class="col-sm-2 control-label">标题</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="inputPassword3" placeholder="标题" name="main_title" value="<?php echo $model->getData('main_title')?>">
            <?php if($model->getError('main_title') != NULL):?>
            <span class="help-block"><?php echo $model->getError('main_title')?></span>
            <?php endIf?>
          </div>
        </div>
        <div class="form-group <?php echo $model->getError('slave_title')!= NULL ? "has-error" : ""?>">
          <label for="inputPassword3" class="col-sm-2 control-label">副标题</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="inputPassword3" placeholder="副标题" name="slave_title" value="<?php echo $model->getData('slave_title')?>">
            <?php if($model->getError('slave_title') != NULL):?>
            <span class="help-block"><?php echo $model->getError('slave_title')?></span>
            <?php endIf?>
          </div>
        </div>
        <div class="form-group <?php echo $model->getError('introduce')!= NULL ? "has-error" : ""?>">
          <label for="inputPassword3" class="col-sm-2 control-label">简介</label>
          <div class="col-sm-10">
            <script id="uecontainer" name="introduce" type="text/plain">
            </script>
            <?php if($model->getError('introduce') != NULL):?>
            <span class="help-block"><?php echo $model->getError('introduce')?></span>
            <?php endIf?>
          </div>
          
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10 submit-btn">
            <button type="submit" class="btn btn-primary" id="submit">发布</button>
          </div>
        </div>
      </form>
    </div>

  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.config2.js"></script>
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/lib/ueditor/ueditor.all.min.js"></script>
  <script type="text/javascript">
  var ue = UE.getEditor('uecontainer');
  ue.ready(function() {
      ue.setContent("<?php echo $model->getData('introduce')?>");
      //获取html内容，返回: <p>hello</p>
      var html = ue.getContent();
      //获取纯文本内容，返回: hello
      var txt = ue.getContentTxt();
  });
  </script>