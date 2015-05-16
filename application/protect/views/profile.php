<div class="row profile-main">
	<div class="col-md-8">
		<h1 class="page-header">基本信息</h1>
		<table class="table table-hover table-bordered">
			<tbody>
				<tr>
					<td>用户名</td>
					<td><?php echo $userInfo['name']?></td>
				</tr>
				<tr>
					<td>加入时间</td>
					<td><?php echo date('Y-m-d H:i:s', $userInfo['add_time'])?></td>
				</tr>
				<tr>
					<td>头像</td>
					<td><img src="<?php echo $userInfo['avatar_url']?>" class="img-responsive" style="max-width: 100px"></td>
				</tr>
				<tr>
					<td>用户角色</td>
					<td><?php echo $userInfo['role_name']?></td>
				</tr>
				<tr>
					<td>可连接</td>
					<td>
						<?php
							if ($userInfo['connectable'] == 0)
							{
								echo "否";
							}
							elseif ($userInfo['connectable'] == 1)
							{
								echo "是";
							}
							else
							{
								echo "未知";
							}
						?>
					</td>
				</tr>
				<tr>
					<td>上次登陆</td>
					<td><?php echo date('Y-m-d H:i:s', $userInfo['last_login_time'])?></td>
				</tr>
				<tr>
					<td>上传量</td>
					<td><?php echo $this->getSize($userInfo['uploaded'])?></td>
				</tr>
				<tr>
					<td>下载量</td>
					<td><?php echo $this->getSize($userInfo['downloaded'])?></td>
				</tr>
				<tr>
					<td>分享率</td>
					<td><?php echo $userInfo['downloaded'] == 0 ? 0 : number_format($userInfo['uploaded']/$userInfo['downloaded'], 3)?></td>
				</tr>
				<tr>
					<td>做种时间</td>
					<td><?php echo $this->getTTL($userInfo['seed_time'], '', $userInfo['seed_time'])?></td>
				</tr>
				<tr>
					<td>下载时间</td>
					<td><?php echo $this->getTTL($userInfo['leech_time'], '', $userInfo['leech_time'])?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php if (!empty($torrentList)):?>
	<div class="col-md-12">
		<h1 class="page-header">发布的种子</h1>
		<table class="table table-bordered table-hover">
			<thead>
	          <tr>
	            <th style="text-align: center">标题</th>
	            <th>添加于</th>
	            <th title="存活时间">TTL</th>
	            <th>大小</th>
	            <th>做种</th>
	            <th>下载</th>
	            <th>完成</th>
	            <th>评论</th>
	            <th>查看</th>
	          </tr>
	        </thead>
			<tbody>
			<?php foreach ($torrentList as $v):?>
	          <tr>
	            <td><a href="<?php echo $this->createUrl('torrent/detail', array('id' => $v['id']))?>"><?php echo $v['main_title']."<br/>".$v['slave_title']?></a></td>
	            <td><?php echo date('Y-m-d', $v['add_time'])."<br/>".date('H:i:s', $v['add_time'])?></td>
	            <td><?php echo $this->getTTL($v['add_time'], '<br/>')?></td>
	            <td><?php echo $this->getSize($v['size'])?></td>
	            <td title="做种"><?php echo $v['seeder_count']?></td>
	            <td title="下载"><?php echo $v['leecher_count']?></td>
	            <td title="完成"><?php echo $v['finish_times']?></td>
	            <td title="评论"><?php echo $v['comment_count']?></td>
	            <td title="查看"><?php echo $v['view_times']?></td>
	          </tr>
	          <?php endForeach?>
			</tbody>
		</table>
	</div>
	<?php endIf?>
	
	<?php if (!empty($threadList)):?>
	<div class="col-md-12">
		<h1 class="page-header">发表的主题</h1>
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>标题</th>
					<th>作者</th>
					<th>回复</th>
					<th>查看</th>
					<th>最近回复</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($threadList as $thread):?>
				<tr>
					<td>
						<strong><a href="<?php echo $this->createUrl('forum/thread/detail', array('section_id' => $thread['section_id'], 'thread_id' => $thread['id'])).$this->getExtraParam(array('section_id'))?>">
						<?php
							if ($thread['is_top']) echo '<span class="glyphicon glyphicon-arrow-up" aria-hidden="true" title="置顶"></span>';
							$draft = '';
							if ($thread['state'] == application\protect\models\ForumthreadModel::STATE_DRAFT) $draft = '【草稿】';
							echo $draft.$thread['title'];
						?>
						</a></strong>
					</td>
					<td><?php echo $userInfo['name']."<br/><small>".date('Y-m-d H:i', $thread['add_time'])."</small>"?></td>
					<td><?php echo $thread['reply_count']?></td>
					<td><?php echo $thread['view_count']?></td>
					<td>
					<?php
						//var_dump($thread['last_reply']);
						if (!empty($thread['last_reply']))
						{
							$reply = unserialize($thread['last_reply']);
							echo  "<a href=\"#\">".$reply['userName']."</a><br/><small>".date('Y-m-d H:i', $reply['addTime'])."</small>";
						}
					?>
					</td>
				</tr>
			<?php endforeach?>
			</tbody>
		</table>
		
	</div>
	<?php endIf?>
</div>