<div class="row">
      <form class="form-horizontal" role="form" id="upload-form" method="post" action="<?php echo $action?>" enctype="multipart/form-data">
        <div class="form-group">
          <div class="col-sm-12">
             <h3 class="text-danger tracker-address">服务器tracker地址是：<?php echo framework\App::ins()->request->getbaseUrl().'announce.php'?></h3>
          </div>
        </div>
        
        <?php if (ACTION === 'Upload'):?>
        <div class="form-group <?php echo $model->getError('torrentFile') != NULL ? "has-error" : ""?>">
          <label for="torrent-file" class="col-sm-2 control-label">种子</label>
          <div class="col-sm-10">
            <input type="file" class="form-control" id="torrent-file" name="torrentFile">
            <?php if($model->getError('torrentFile') != NULL):?>
            <span class="help-block"><?php echo $model->getError('torrentFile')?></span>
            <?php endIf?>
          </div>
        </div>
        <?php else:?>
        <div class="form-group">
          <label for="torrent-file" class="col-sm-2 control-label">种子</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" value="<?php echo $this->getTorrentName($model->getData('name'))?>" readonly> 
          </div>
        </div>
        <?php endIf?>
            
        <div class="form-group <?php echo $model->getError('main_title')!= NULL ? "has-error" : ""?>">
          <label for="mainTitle" class="col-sm-2 control-label">标题</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="main_title" placeholder="若不填写，会取种子的文件名" name="main_title" value="<?php echo $model->getData('main_title')?>">
            <?php if($model->getError('main_title') != NULL):?>
            <span class="help-block"><?php echo $model->getError('main_title')?></span>
            <?php endIf?>
          </div>
        </div>
        
        <div class="form-group <?php echo $model->getError('slave_title')!= NULL ? "has-error" : ""?>">
          <label for="slave_title" class="col-sm-2 control-label">副标题</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="slave_title" placeholder="副标题，必须" name="slave_title" value="<?php echo $model->getData('slave_title')?>">
            <?php if($model->getError('slave_title') != NULL):?>
            <span class="help-block"><?php echo $model->getError('slave_title')?></span>
            <?php endIf?>
          </div>
        </div>
        
        <div class="form-group <?php echo $model->getError('douban_id')!= NULL ? "has-error" : ""?>">
          <label for="douban_id" class="col-sm-2 control-label">豆瓣ID</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="douban_id" placeholder="豆瓣网上的对应ID，用于请求基本信息。可选，尽量填写且保证正确" name="douban_id" value="<?php echo $model->getData('douban_id') == NULL ? '' : $model->getData('douban_id')?>">
            <?php if($model->getError('douban_id') != NULL):?>
            <span class="help-block"><?php echo $model->getError('douban_id')?></span>
            <?php endIf?>
          </div>
        </div>
        
        <div class="form-group <?php echo $model->getError('imdb_id')!= NULL ? "has-error" : ""?>">
          <label for="imdb_id" class="col-sm-2 control-label">IMDB ID</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="imdb_id" placeholder="IMDB网上的对应ID，用于请求基本信息。可远，尽量填写且保证正确" name="imdb_id" value="<?php echo $model->getData('imdb_id') == NULL ? '' : $model->getData('imdb_id')?>">
            <?php if($model->getError('imdb_id') != NULL):?>
            <span class="help-block"><?php echo $model->getError('imdb_id')?></span>
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
          
          <!-- 筛选条件/种子分类开始 -->
          
          <?php echo $this->createCategoryFormGroup($model)?>
          
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10 submit-btn">
            <button type="submit" class="btn btn-primary" id="submit"><?php echo ACTION === 'Upload' ? '发布' : '编辑'?></button>
          </div>
        </div>
        
        <?php if(!empty($_GET['id'])):?>
        <input type="hidden" name="id" value="<?php echo $_GET['id']?>">
        <?php endIf?>
      </form>
    </div>
  <?php echo $this->getScript('application/assets/lib/ueditor/ueditor.config2.js')?>
  <script src="<?php echo framework\App::ins()->request->getBaseUrl()?>application/assets/lib/ueditor/ueditor.all.min.js"></script>
  <script type="text/javascript">
  var ue = UE.getEditor('uecontainer');
  ue.ready(function() {
      ue.setContent('<?php echo $model->getData('introduce')?>');
      //获取html内容，返回: <p>hello</p>
      var html = ue.getContent();
      //获取纯文本内容，返回: hello
      var txt = ue.getContentTxt();
  });
  </script>