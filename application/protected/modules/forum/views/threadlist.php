<nav class="forum-thread-nav">
		<a href="<?php echo $this->createUrl('forum/thread/add', array('section_id' => $sectionId))?>" class="btn btn-success pull-left"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>发表主题</a>
	  <ul class="pagination">
	  	<li><a href="<?php echo $this->createUrl('forum/section/list')?>"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>返回</a></li>
	    <li>
	      <a href="#" aria-label="Previous">
	        <span aria-hidden="true">&laquo;</span>
	      </a>
	    </li>
	    <li><a href="#">1</a></li>
	    <li><a href="#">2</a></li>
	    <li><a href="#">3</a></li>
	    <li><a href="#">4</a></li>
	    <li><a href="#">5</a></li>
	    <li>
	      <a href="#" aria-label="Next">
	        <span aria-hidden="true">&raquo;</span>
	      </a>
	    </li>
	  </ul>
</nav>

<table class="table forum-thread-table">
	<thead>
		<tr>
			<th>标题</th>
			<th>作者</th>
			<th>回复</th>
			<th>查看</th>
			<th>最近回复</th>
	</thead>
	<tbody>
	<?php if (!empty($threadList)):?>
	<?php foreach ($threadList as $thread):?>
		<tr>
			<td><strong><a href="<?php echo $this->createUrl('forum/thread/detail', array('section_id' => $sectionId, 'thread_id' => $thread['id']))?>"><?php echo $thread['title']?></a></strong></td>
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
		<a href="<?php echo $this->createUrl('forum/thread/add', array('section_id' => $sectionId))?>" class="btn btn-success pull-left"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>发表主题</a>
	  <ul class="pagination">
	  	<li><a href="<?php echo $this->createUrl('forum/section/list')?>"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>返回</a></li>
	    <li>
	      <a href="#" aria-label="Previous">
	        <span aria-hidden="true">&laquo;</span>
	      </a>
	    </li>
	    <li><a href="#">1</a></li>
	    <li><a href="#">2</a></li>
	    <li><a href="#">3</a></li>
	    <li><a href="#">4</a></li>
	    <li><a href="#">5</a></li>
	    <li>
	      <a href="#" aria-label="Next">
	        <span aria-hidden="true">&raquo;</span>
	      </a>
	    </li>
	  </ul>
</nav>