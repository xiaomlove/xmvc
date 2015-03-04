<?php require 'head.php'?>
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<div class="container">
    <div class="row">
      <ul class="nav nav-pills" id="main-nav">
        <li role="presentation" <?php if(CONTROLLER === 'Index' && ACTION === 'Home') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('index/home')?>">首页</a></li>
        <li role="presentation" <?php if(CONTROLLER === 'Forum') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('forum/section')?>">论坛</a></li>
        <li role="presentation" <?php if(CONTROLLER === 'Torrent' && ACTION === 'List') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('torrent/list')?>">种子</a></li>
        <li role="presentation" <?php if(CONTROLLER === 'Torrent' && ACTION === 'Upload') echo "class=\"active\""?>><a href="<?php echo $this->createUrl('torrent/upload')?>">发布</a></li>
      </ul>
    </div>

<?php echo $content?>

</div>

<?php require 'foot.php'?>

