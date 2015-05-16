<h3 class="main-title">
	<strong>添加版块</strong>
	<?php if ($model->getData('parent_id') != NULL):?>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/forum/sectionlist', array('parent_id' => $model->getData('parent_id')))?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>返回列表</a>
	<?php else:?>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/forum/sectionlist')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>返回列表</a>
	<?php endIf?>
</h3>

<form class="form-horizontal" action="<?php echo $action?>" method="post">
  <div class="form-group <?php echo $model->hasError('name') ? "has-error" : ""?>">
    <label for="name" class="col-sm-2 control-label">名称</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="name" name="name" value="<?php echo $model->getData('name')?>" placeholder="版块名称">
      <?php if($model->hasError('name')):?>
      	<span class="help-block"><?php echo $model->getError('name')?></span>
      <?php endIf?>
    </div>
  </div>
  <div class="form-group <?php echo $model->hasError('parent_id') ? "has-error" : ""?>">
    <label for="parent_id" class="col-sm-2 control-label">父版块</label>
    <div class="col-sm-10">
      <?php echo $this->getParentSelect($model->getData('parent_id'),$model->getData('id'))?>
      <?php if($model->hasError('parent_id')):?>
      	<span class="help-block"><?php echo $model->getError('parent_id')?></span>
      <?php endIf?>
    </div>
  </div>
  <div class="form-group <?php echo $model->hasError('view_level_limit') ? "has-error" : ""?>">
    <label for="view_level_limit" class="col-sm-2 control-label">查看最低等级</label>
    <div class="col-sm-10">
      <?php echo $this->getRoleSelect('view_level_limit', $model->getData('view_level_limit'))?>
      <?php if($model->hasError('view_level_limit')):?>
      	<span class="help-block"><?php echo $model->getError('view_level_limit')?></span>
      <?php endIf?>
    </div>
  </div>
    <div class="form-group <?php echo $model->hasError('reply_level_limit') ? "has-error" : ""?>">
    <label for="reply_level_limit" class="col-sm-2 control-label">回复最低等级</label>
    <div class="col-sm-10">
      <?php echo $this->getRoleSelect('reply_level_limit', $model->getData('reply_level_limit'))?>
      <?php if($model->hasError('reply_level_limit')):?>
      	<span class="help-block"><?php echo $model->getError('reply_level_limit')?></span>
      <?php endIf?>
    </div>
  </div>
    <div class="form-group <?php echo $model->hasError('publish_level_limit') ? "has-error" : ""?>">
    <label for="publish_level_limit" class="col-sm-2 control-label">发表最低等级</label>
    <div class="col-sm-10">
      <?php echo $this->getRoleSelect('publish_level_limit', $model->getData('publish_level_limit'))?>
      <?php if($model->hasError('publish_level_limit')):?>
      	<span class="help-block"><?php echo $model->getError('publish_level_limit')?></span>
      <?php endIf?>
    </div>
  </div>
   <div class="form-group">
    <label for="description" class="col-sm-2 control-label">版块描述</label>
    <div class="col-sm-10">
      <textarea class="form-control" rows="3" placeholder="适当描述性文字" id="description" name="description"><?php echo $model->getData('description')?></textarea>
    </div>
  </div>
  
  <div class="form-group <?php echo $model->hasError('sort') ? "has-error" : ""?>">
    <label for="sort" class="col-sm-2 control-label">排序</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="sort" name="sort" value="<?php echo $model->getData('sort')?>"  placeholder="数值越大越靠后">
      <?php if($model->hasError('sort')):?>
      	<span class="help-block"><?php echo $model->getError('sort')?></span>
      <?php endIf?>
    </div>
  </div>
  
 <div class="form-group <?php echo $model->hasError('master_name_list') ? "has-error" : ""?>">
    <label for="master_name_list" class="col-sm-2 control-label">版主</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="master_name_list" name="master_name_list" value="<?php echo $model->getData('master_name_list')?>" placeholder="填写用户名，多个空格割开，最多3个">
      <?php if($model->hasError('master_name_list')):?>
      	<span class="help-block"><?php echo $model->getError('master_name_list')?></span>
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
