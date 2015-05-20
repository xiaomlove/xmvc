<h3 class="main-title" id="main-title">
	<strong>用户详情</strong>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/user/userlist')?>"><span class="glyphicon glyphicon-arrow-left"></span>返回列表</a>
</h3>

<div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#basic-info" aria-controls="basic-info" role="tab" data-toggle="tab">基本信息</a></li>
    <li role="presentation"><a href="#upload-torrents" aria-controls="upload-torrents" role="tab" data-toggle="tab">发布的种子 </a></li>
    <li role="presentation"><a href="#download-torrents" aria-controls="download-torrents" role="tab" data-toggle="tab">下载的种子</a></li>
    <li role="presentation"><a href="#comments" aria-controls="comments" role="tab" data-toggle="tab">发表的评论</a></li>
    <li role="presentation"><a href="#threads" aria-controls="threads" role="tab" data-toggle="tab">发表的主题</a></li>
    <li role="presentation"><a href="#replys" aria-controls="replys" role="tab" data-toggle="tab">发表的回复</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="basic-info">
    	<table class="table table-bordered table-hover" style="margin-top: 50px">
    		<thead>
    			<th>项目名称</th>
    			<th>项目的值</th>
    			<th>相关操作</th>
    		</thead>
    		<tbody>
	    		<tr><td>ID</td><td><?php echo $userInfo['id']?></td></tr>
	    		<tr><td>用户名称</td><td><?php echo $userInfo['name']?></td><td><a href="#">修改</a></td></tr>
	    		<tr><td>密码</td><td>********</td><td><a href="#">重置</a></td></tr>
	    		<tr><td>所属角色</td><td>
	    		<?php 
	    			if (!empty($roles))
	    			{
	    				$out = '';
	    				foreach ($roles as $role)
	    				{
	    					$out .= $role['name'].'('.$role['role_group_name'].'),';
	    				}
	    				echo rtrim($out, ',');
	    			}
	    		?>
	    		</td><td><a href="#">添加</a></td></tr>
	    		<tr><td>额外权限</td><td>
	    		<?php
	    			if (!empty($extraRules))
	    			{
	    				$out = '';
	    				foreach ($extraRules as $extraRule)
	    				{
	    					$out .= $extraRule['name'].'('.$extraRule['role_group_name'].'),';
	    				}
	    				echo rtrim($out, ',');
	    			}
	    		?>
	    		</td><td><a href="#">添加</a></td></tr>
	    		<tr><td>头像</td><td><img src="/<?php echo $userInfo['avatar_url']?>"></td><td><a href="#">修改</a></td></tr>
    		</tbody>
    	</table>
    </div>
    <div role="tabpanel" class="tab-pane" id="upload-torrents">...</div>
    <div role="tabpanel" class="tab-pane" id="download-torrents">...</div>
    <div role="tabpanel" class="tab-pane" id="comments">...</div>
    <div role="tabpanel" class="tab-pane" id="threads">...</div>
    <div role="tabpanel" class="tab-pane" id="replys">...</div>
  </div>

</div>

<script>
	
</script>