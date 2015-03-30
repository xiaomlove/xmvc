<?php require 'head.php'?>
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<div class="container">
    <div class="row">
      <ul class="nav nav-pills" id="main-nav">
        <li role="presentation" <?php if(CONTROLLER === 'Index' && ACTION === 'Home') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('index/home')?>">首页</a></li>
        <li role="presentation" <?php if(MODULE === 'Forum') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('forum/section/list')?>">论坛</a></li>
        <li role="presentation" <?php if(CONTROLLER === 'Torrent' && (ACTION === 'List' || ACTION === 'Detail')) echo "class=\"active\""?>><a href="<?php echo $this->createUrl('torrent/list')?>">种子</a></li>
        <li role="presentation" <?php if(CONTROLLER === 'Torrent' && ACTION === 'Upload') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('torrent/upload')?>">发布</a></li>
        <li role="presentation" <?php if(MODULE === 'Manage') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('manage/index/index')?>">管理</a></li>
        <?php if (App::ins()->user->isLogin()):?>
        <li class="user-box"><a href="<?php echo $this->createUrl('profile')?>"><?php echo App::ins()->user->getName()?></a><a href="" data-toggle="modal" data-target=".bs-example-modal-sm">(退出)</a></li>
        <?php else:?>
        <li class="user-box"><a href=""><?php echo App::ins()->user->getName()?></a><a href="<?php echo $this->createUrl('index/login')?>">(登陆)</a></li>
        <?php endIf?>
      </ul>
    </div>
	<h3 class="page-header" id="forum-nav" style="text-align: center">
		<?php echo $this->getBreadcrumbs()?>
	</h3>
	<?php echo $content?>

</div>

<?php require 'foot.php'?>

