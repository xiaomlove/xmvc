<h3 class="main-title">
	<?php if (ACTION === 'Addsub'):?>
	<strong>为【<?php echo $parent['name']?>】添加子项目</strong>
	<?php else:?>
	<strong>编辑【<?php echo $category['name']?>】</strong>
	<?php endIf?>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/category/sublist', array('parent_id' => $parentId))?>"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>返回列表</a>
</h3>

<form class="form-horizontal" action="" method="post">
  <div class="form-group <?php echo $model->hasError('name') ? "has-error" : ""?>">
    <label for="name" class="col-sm-2 control-label">名称</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="name" name="name" value="<?php echo $model->getData('name')?>" placeholder="">
      <?php if($model->hasError('name')):?>
      	<span class="help-block"><?php echo $model->getError('name')?></span>
      <?php endIf?>
    </div>
  </div>
  
  
  
  <div class="form-group <?php echo $model->hasError('value') ? "has-error" : ""?>">
    <label for="value" class="col-sm-2 control-label">torrent值</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="value" name="value" value="<?php echo $model->getData('value')?>" placeholder="">
      <?php if($model->hasError('value')):?>
      	<span class="help-block"><?php echo $model->getError('value')?></span>
      <?php endIf?>
    </div>
  </div>
  
	<input type="hidden" value="<?php echo $parentId?>" name="parent_id"> 
  <?php if (isset($_GET['id'])):?>
  <input type="hidden" value="<?php echo $_GET['id']?>" name="id">
  <?php endIf?>
  <div class="form-group">
    <div class="col-sm-offset-6 col-sm-6">
      <button type="submit" class="btn btn-primary">确定</button>
    </div>
  </div>
</form>
