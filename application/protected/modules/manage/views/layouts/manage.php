<?php require 'head.php'?>
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<div class="container">
    <div class="row">
      <ul class="nav nav-pills" id="main-nav">
        <li role="presentation" <?php if(CONTROLLER === 'Index' && ACTION === 'Home') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('index/home')?>">首页</a></li>
        <li role="presentation" <?php if(CONTROLLER === 'Forum' && MODULE === NULL) echo "class=\"active\""?>><a href="<?php echo $this->createUrl('forum/section/list')?>">论坛</a></li>
        <li role="presentation" <?php if(CONTROLLER === 'Torrent' && ACTION === 'List') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('torrent/list')?>">种子</a></li>
        <li role="presentation" <?php if(CONTROLLER === 'Torrent' && ACTION === 'Upload') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('torrent/upload')?>">发布</a></li>
        <li role="presentation" <?php if(MODULE === 'Manage') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('manage/index/index')?>">管理</a></li>
      </ul>
    </div>
</div>
<div class="container-fluid manage">
	<div class="row">
		<div class="col-md-1 manage-left">
			<ul class="list-unstyled">
				<li <?php if(CONTROLLER === 'User' && ACTION === 'Userlist') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('manage/user/userlist')?>">用户列表</a></li>
				<li <?php if(CONTROLLER === 'Role' && ACTION === 'Rolelist') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('manage/role/rolelist')?>">用户角色</a></li>
				<li <?php if(CONTROLLER === 'Forum' && ACTION === 'Sectionlist') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('manage/forum/sectionlist')?>">论坛版块</a></li>
				<li <?php if(CONTROLLER === 'Option' && ACTION === 'Pagination') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('manage/option/forumset')?>">论坛设置</a></li>
			</ul>
		</div>
		<div class="col-md-offset-1 col-md-9 manage-main">
			<?php echo $content?>
		</div>
	</div>
</div>


<?php require 'foot.php'?>

