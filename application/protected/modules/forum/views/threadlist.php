<nav class="forum-thread-nav">
		<a href="<?php echo $this->createUrl('forum/thread/add', array('section_id' => $sectionId))?>" class="btn btn-info pull-left"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>发表主题</a>
		<a href="<?php echo $this->createUrl('forum/thread/list', array('section_id' => $sectionId, 'filter' => 'add_time'))?>" class="btn btn-sm pull-left <?php echo !empty($_GET['filter']) && $_GET['filter'] === 'add_time' ? "btn-primary" : "bg-light-gray"?>">最新发表</a>
		<!--<a href="<?php echo $this->createUrl('forum/thread/list', array('section_id' => $sectionId, 'filter' => 'support_count'))?>" class="btn btn-sm pull-left <?php echo !empty($_GET['filter']) && $_GET['filter'] === 'support_count' ? "btn-primary" : "bg-light-gray"?>">最多支持</a>  -->
	  <?php echo $navHtml?>
</nav>

<table class="table forum-thread-table">
	<thead>
		<tr>
			<th>标题</th>
			<th>作者</th>
			<th><?php echo $this->getSortHref('reply_count', '回复')?></th>
			<th><?php echo $this->getSortHref('view_count', '查看')?></th>
			<th><?php echo $this->getSortHref('last_reply_time', '最近回复')?></th>
		</tr>
	</thead>
	<tbody>
	<?php if (!empty($threadList)):?>
	<?php foreach ($threadList as $thread):?>
		<tr>
			<td>
				<strong><a href="<?php echo $this->createUrl('forum/thread/detail', array('section_id' => $sectionId, 'thread_id' => $thread['id'])).$this->getExtraParam(array('section_id'))?>">
				<?php
					if ($thread['is_top']) echo '<span class="glyphicon glyphicon-arrow-up" aria-hidden="true" title="置顶"></span>';
					echo $thread['title'];
				?>
				</a></strong>
			</td>
			<td><?php echo $thread['user_name']."<br/><small>".date('Y-m-d H:i', $thread['add_time'])."</small>"?></td>
			<td><?php echo $thread['reply_count']?></td>
			<td><?php echo $thread['view_count']?></td>
			<td>
			<?php
				if (!empty($thread['last_reply']))
				{
					$reply = unserialize($thread['last_reply']);
					echo  "<a href=\"#\">".$reply['userName']."</a><br/><small>".date('Y-m-d H:i', $reply['addTime'])."</small>";
				}
			?>
			</td>
		</tr>
	<?php endforeach?>
	<?php else:?>
		<tr><td colspan="5">暂无主题</td></tr>
	<?php endIf?>
	</tbody>
</table>
<nav class="forum-thread-nav">
		<a href="<?php echo $this->createUrl('forum/thread/add', array('section_id' => $sectionId))?>" class="btn btn-info pull-left"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>发表主题</a>
	  <?php echo $navHtml?>
</nav>