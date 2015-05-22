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
	    		<tr><td>ID</td><td><?php echo $userInfo['id']?></td><td></td></tr>
	    		<tr><td>用户名称</td><td><?php echo $userInfo['name']?></td><td><a href="#">修改</a></td></tr>
	    		<tr><td>密码</td><td>********</td><td><a href="#">重置</a></td></tr>
	    		<tr><td>账号状态</td><td>
	    		<?php 
	    			if ($userInfo['state'] == 0)
	    			{
	    				echo '禁用';
	    			}
	    			elseif ($userInfo['state'] == 2)
	    			{
	    				echo '挂起';
	    			}
	    			elseif ($userInfo['state'] == 1)
	    			{
	    				echo '正常';
	    			}
	    			else
	    			{
	    				echo '出错';
	    			}
	    		?>
	    		</td><td><a href="#">修改</a></td></tr>
	    		<tr><td>加入时间</td><td><?php echo date('Y-m-d H:i', $userInfo['add_time'])?></td><td></td></tr>
	    		<tr><td>上次登陆</td><td><?php echo date('Y-m-d H:i', $userInfo['last_login_time'])?></td><td></td></tr>
	    		<tr><td>最近登陆</td><td><?php echo date('Y-m-d H:i', $userInfo['this_login_time'])?></td><td></td></tr>
	    		<tr><td>当前可连接</td><td>
	    		<?php 
	    			if ($userInfo['connectable'] == 0)
	    			{
	    				echo '否';
	    			}
	    			elseif ($userInfo['connectable'] == 1)
	    			{
	    				echo '是';
	    			}
	    			else 
	    			{
	    				echo '未知';
	    			}
	    		?></td><td></td></tr>
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
	    		<tr><td>头像</td><td><img src="/<?php echo $userInfo['avatar_url']?>" style="width: 50px"></td><td><a href="#">修改</a></td></tr>
	    		<tr><td>上传量</td><td><?php echo $this->getSize($userInfo['uploaded'])?></td><td><a href="#">增加</a></td></tr>
	    		<tr><td>下载量</td><td><?php echo $this->getSize($userInfo['downloaded'])?></td><td><a href="#">增加</a></td></tr>
	    		<tr><td>分享率</td><td>
	    		<?php 
	    			if ($userInfo['downloaded'])
	    			{
	    				echo number_format($userInfo['uploaded']/$userInfo['downloaded'], 3);
	    			}
	    			else
	    			{
	    				echo 0;
	    			}
	    		?>
	    		</td><td></td></tr>
	    		<tr><td>魔力值</td><td><?php echo $userInfo['bonus']?></td><td><a href="#">增加</a></td></tr>
	    		<tr><td>做种时间</td><td><?php echo $this->getTTL($userInfo['seed_time'], '', $userInfo['seed_time'])?></td><td></td></tr>
	    		<tr><td>下载时间</td><td><?php echo $this->getTTL($userInfo['leech_time'], '', $userInfo['leech_time'])?></td><td></td></tr>
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