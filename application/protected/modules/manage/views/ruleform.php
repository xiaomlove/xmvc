<h3 class="main-title">
	<?php if (ACTION === 'Add'):?>
	<strong>添加权限</strong>
	<?php else:?>
	<strong>编辑权限</strong>
	<?php endIf?>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/rule/list')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>返回列表</a>
</h3>

<form class="form-horizontal" action="" method="post">
  <div class="form-group <?php echo $model->hasError('name') ? "has-error" : ""?>">
    <label for="name" class="col-sm-2 control-label">权限名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="name" name="name" value="<?php echo $model->getData('name')?>" placeholder="">
      <?php if($model->hasError('name')):?>
      	<span class="help-block"><?php echo $model->getError('name')?></span>
      <?php endIf?>
    </div>
  </div>
  <div class="form-group <?php echo $model->hasError('rule_key') ? "has-error" : ""?>">
    <label for="rule_key" class="col-sm-2 control-label">权限key</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="rule_key" name="rule_key" value="<?php echo $model->getData('rule_key')?>" placeholder="唯一的key，只能为英文字母">
      <?php if($model->hasError('rule_key')):?>
      	<span class="help-block"><?php echo $model->getError('rule_key')?></span>
      <?php endIf?>
    </div>
  </div>
  
  <div class="form-group <?php echo $model->hasError('rule_mvc') ? "has-error" : ""?>">
    <label for="rule_mvc" class="col-sm-2 control-label">权限mvc</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="rule_mvc" name="rule_mvc" value="<?php echo $model->getData('rule_mvc')?>" placeholder="对应操作的MVC，确保正确，否则无效">
      <?php if($model->hasError('rule_mvc')):?>
      	<span class="help-block"><?php echo $model->getError('rule_mvc')?></span>
      <?php endIf?>
    </div>
  </div>
  
   <div class="form-group <?php echo $model->hasError('sort') ? "has-error" : ""?>">
    <label for="sort" class="col-sm-2 control-label">排序</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="sort" name="sort" value="<?php echo $model->getData('sort')?>" placeholder="影响在列表中的显示先后顺序">
      <?php if($model->hasError('sort')):?>
      	<span class="help-block"><?php echo $model->getError('sort')?></span>
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
