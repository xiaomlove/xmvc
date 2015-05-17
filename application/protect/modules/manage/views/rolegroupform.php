<h3 class="main-title">
	<?php if (ACTION === 'Add'):?>
	<strong>添加用户组</strong>
	<?php else:?>
	<strong>编辑用户组</strong>
	<?php endIf?>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/rolegroup/list')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>返回列表</a>
</h3>

<form class="form-horizontal" action="" method="post">
  <?php if (isset($_GET['id'])):?>
  <div class="form-group <?php echo $model->hasError('id') ? "has-error" : ""?>">
    <label for="id" class="col-sm-2 control-label">用户组ID</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="id" name="id" value="<?php echo $model->getData('id')?>" placeholder="" readonly>
      <?php if($model->hasError('id')):?>
      	<span class="help-block"><?php echo $model->getError('id')?></span>
      <?php endIf?>
    </div>
  </div>
  <?php endIf?>
  
  <div class="form-group <?php echo $model->hasError('name') ? "has-error" : ""?>">
    <label for="name" class="col-sm-2 control-label">名称</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="name" name="name" value="<?php echo $model->getData('name')?>" placeholder="">
      <?php if($model->hasError('name')):?>
      	<span class="help-block"><?php echo $model->getError('name')?></span>
      <?php endIf?>
    </div>
  </div>
  
  
  <?php if (isset($_GET['id'])):?>
  <input type="hidden" value="<?php echo $_GET['id']?>" name="id">
  <?php endIf?>
  <div class="form-group">
    <div class="col-sm-offset-6 col-sm-6">
      <button type="submit" class="btn btn-primary">确定</button>
    </div>
  </div>
</form>
