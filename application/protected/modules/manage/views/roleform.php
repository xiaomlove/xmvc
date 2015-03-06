<h3 class="main-title">
	<strong>添加角色</strong>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/role/rolelist')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>返回列表</a>
</h3>

<form class="form-horizontal" action="<?php echo $this->createUrl('manage/role/roleadd')?>" method="post">
  <div class="form-group <?php echo $model->getError('name') != NULL ? "has-error" : ""?>">
    <label for="name" class="col-sm-2 control-label">角色名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="name" name="name" value="<?php echo $model->getData('name')?>" placeholder="">
      <?php if($model->getError('name') != NULL):?>
      	<span class="help-block"><?php echo $model->getError('name')?></span>
      <?php endIf?>
    </div>
  </div>
  <div class="form-group <?php echo $model->hasError('bonus_limit') ? "has-error" : ""?>">
    <label for="bonus_limit" class="col-sm-2 control-label">魔力下限</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="bonus_limit" name="bonus_limit" value="<?php echo $model->getData('bonus_limit')?>" placeholder="">
      <?php if($model->getError('bonus_limit') != NULL):?>
      	<span class="help-block"><?php echo $model->getError('bonus_limit')?></span>
      <?php endIf?>
    </div>
  </div>
  
  <div class="form-group <?php echo $model->getError('downloaded_limit') != NULL ? "has-error" : ""?>">
    <label for="downloaded_limit" class="col-sm-2 control-label">下载量下限</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="downloaded_limit" name="downloaded_limit" value="<?php echo $model->getData('downloaded_limit')?>" placeholder="">
      <?php if($model->getError('downloaded_limit') != NULL):?>
      	<span class="help-block"><?php echo $model->getError('downloaded_limit')?></span>
      <?php endIf?>
    </div>
  </div>
  
  <div class="form-group <?php echo $model->getError('uploaded_limit') != NULL ? "has-error" : ""?>">
    <label for="uploaded_limit" class="col-sm-2 control-label">上传量下限</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="uploaded_limit" name="uploaded_limit" value="<?php echo $model->getData('uploaded_limit')?>" placeholder="">
      <?php if($model->getError('uploaded_limit') != NULL):?>
      	<span class="help-block"><?php echo $model->getError('uploaded_limit')?></span>
      <?php endIf?>
    </div>
  </div>
  
  <div class="form-group <?php echo $model->getError('ratio_limit') != NULL ? "has-error" : ""?>">
    <label for="ratio_limit" class="col-sm-2 control-label">分享率下限</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="ratio_limit" name="ratio_limit" value="<?php echo $model->getData('ratio_limit')?>" placeholder="">
      <?php if($model->getError('ratio_limit') != NULL):?>
      	<span class="help-block"><?php echo $model->getError('ratio_limit')?></span>
      <?php endIf?>
    </div>
  </div>
 
  <div class="form-group <?php echo $model->getError('register_time_limit') != NULL ? "has-error" : ""?>">
    <label for="register_time_limit" class="col-sm-2 control-label">注册时间下限</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" id="register_time_limit" name="register_time_limit" value="<?php echo $model->getData('register_time_limit')?>" placeholder="">
      <?php if($model->getError('register_time_limit') != NULL):?>
      	<span class="help-block"><?php echo $model->getError('register_time_limit')?></span>
      <?php endIf?>
    </div>
    <div class="col-sm-2">
    	<select class="form-control" name="unit">
      	  <option value="week" <?php if ($model->getData('unit') === 'week') echo "selected"?>>周</option>
		  <option value="month" <?php if ($model->getData('unit') === 'month') echo "selected"?>>月</option>
		  <option value="year" <?php if ($model->getData('unit') === 'year') echo "selected"?>>年</option>
		</select>
    </div>
  </div>

  <div class="form-group <?php echo $model->getError('level') != NULL ? "has-error" : ""?>">
    <label for="level" class="col-sm-2 control-label">所属等级</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="level" name="level" value="<?php echo $model->getData('level')?>" placeholder="角色对应的等级，值越大等级越高，要求应该越高。中间的等级各数值也就处于中间">
      <?php if($model->getError('level') != NULL):?>
      	<span class="help-block"><?php echo $model->getError('level')?></span>
      <?php endIf?>
    </div>
  </div> 
  <div class="form-group">
    <div class="col-sm-offset-6 col-sm-6">
      <button type="submit" class="btn btn-primary">确定</button>
    </div>
  </div>
</form>
