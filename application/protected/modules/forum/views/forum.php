<?php if (!empty($sectionList)):?>
<table class="table table-bordered forum-section-table">
	<?php foreach ($sectionList as $section):?>
	<?php if ($section['parent_id'] == 0):?>
	<thead>
		<tr>
			<th><?php echo $section['name']?></th>
			<th>主题数</th>
			<th>回复数</th>
			<th>最近回复</th>
			<th>版主</th>
		</tr>
	</thead>
	<?php else:?>
	<tbody>
		<tr>
			<td><?php echo "<a href=\"".$this->createUrl('forum/thread/list', array('section_id' => $section['id']))."\"><strong>".$section['name']."</strong></a><em>(今日：<i class=\"forum-section-today\">".($section['thread_today_count']+$section['reply_today_count'])."</i>)</em><br/>".$section['description']?></td>
			<td><?php echo $section['thread_total_count']?></td>
			<td><?php echo $section['reply_total_count']?></td>
			<td>
				<?php 
					if (!empty($section['last_reply']))
					{
						$reply = unserialize($section['last_reply']);
						$result = "<a href=\"".$this->createUrl('forum/thread/detail', array('section_id' => $reply['sectionId'], 'thread_id' => $reply['threadId']))."\">".strip_tags($reply['content'])."</a></br>";
						$result .= "<a href=\"#\" style=\"color: #333\">".$reply['userName']."</a>&nbsp;&nbsp;|&nbsp;&nbsp;<small title=\"".date('Y-m-d H:i', $reply['addTime'])."\">".$this->getTTL($reply['addTime'], '')."前</small>";
						echo $result;
					}
				?>
			</td>
			<td><?php echo $section['master_name_list']?></td>
		</tr>
	</tbody>
	<?php endIF?>
	<?php endforeach?>
</table>
<?php else:?>
<h1 class="text-danger">暂无任何版块可以显示，请先到管理页面添加</h1>
<?php endIf?>