<h3 class="main-title">
	<strong>添加版块</strong>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/forum/sectionlist')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>返回列表</a>
</h3>

<form class="form-horizontal">
  <div class="form-group">
    <label for="name" class="col-sm-2 control-label">名称</label>
    <div class="col-sm-10">
      <input type="email" class="form-control" id="name" name="name" placeholder="版块名称">
    </div>
  </div>
  <div class="form-group">
    <label for="parent" class="col-sm-2 control-label">父版块</label>
    <div class="col-sm-10">
      <select class="form-control" id="parent" name="parent">
      	  <option>无(新增为一级版块)</option>
		  <option>官方发布区</option>
		  <option>综合讨论区</option>
	
		</select>
    </div>
  </div>
  <div class="form-group">
    <label for="view-level" class="col-sm-2 control-label">查看最低等级</label>
    <div class="col-sm-10">
      <select class="form-control" id="view-level" name="view-level">
      	  <option>选择一个等级...</option>
		  <option>平民</option>
		  <option>秀才</option>
		  <option>状元</option>
		  <option>员外</option>
		  <option>丞相</option>
		</select>
    </div>
  </div>
    <div class="form-group">
    <label for="reply-level" class="col-sm-2 control-label">回复最低等级</label>
    <div class="col-sm-10">
      <select class="form-control" id="reply-level" name="reply-level">
      	  <option>选择一个等级...</option>
		  <option>平民</option>
		  <option>秀才</option>
		  <option>状元</option>
		  <option>员外</option>
		  <option>丞相</option>
		</select>
    </div>
  </div>
    <div class="form-group">
    <label for="post-level" class="col-sm-2 control-label">发表最低等级</label>
    <div class="col-sm-10">
      <select class="form-control" id="post-level" name="post-level">
      	  <option>选择一个等级...</option>
		  <option>平民</option>
		  <option>秀才</option>
		  <option>状元</option>
		  <option>员外</option>
		  <option>丞相</option>
		</select>
    </div>
  </div>
   <div class="form-group">
    <label for="description" class="col-sm-2 control-label">版块描述</label>
    <div class="col-sm-10">
      <textarea class="form-control" rows="3" placeholder="适当描述性文字" id="description" name="description"></textarea>
    </div>
  </div>
  
  <div class="form-group">
    <label for="sort" class="col-sm-2 control-label">排序</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="sort" name="sort" placeholder="数值越大越靠后">
    </div>
  </div>
  
 <div class="form-group">
    <label for="master" class="col-sm-2 control-label">版主</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="master" name="master" placeholder="填写用户名，多个空格割开，最多3个">
    </div>
  </div>
 
  <div class="form-group">
    <div class="col-sm-offset-6 col-sm-6">
      <button type="submit" class="btn btn-primary">确定</button>
    </div>
  </div>
</form>
