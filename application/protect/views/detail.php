
<div class="row torrent-detail">
      <h1 class="torrent-title"><?php echo $torrent['main_title']?></h1>
      <?php if(framework\App::ins()->user->hasFlash('upload_success')):?>
      <h1 id="upload-success-flash" class="torrent-title text-danger"><strong><?php echo framework\App::ins()->user->getFlash('upload_success')?></strong></h1>
      <?php endIf?>
      <table class="table table-bordered" id="detail-table">

        <tbody>
          <tr>
            <td class="hidden-sm hidden-xs">下载</td>
            <td><a href="download?id=<?php echo $torrent['id']?>"><?php echo $this->getTorrentName($torrent['name'])?></a></td>
          </tr>
          <tr>
            <td class="hidden-sm hidden-xs">副标题</td>
            <td><?php echo $torrent['slave_title']?></td>
          </tr>
          <tr>
            <td class="hidden-sm hidden-xs">基本信息</td>
            <td>
            	大小：<strong><?php echo $this->getSize($torrent['size'])?></strong>
            	类型：<strong><?php echo $this->getCategory('resource_type', $torrent['resource_type'])?></strong>
            	媒介：<strong><?php echo $this->getCategory('resource_medium', $torrent['resource_medium'])?></strong>
            	视频编码：<strong><?php echo $this->getCategory('video_encode', $torrent['video_encode'])?></strong>
            	音频编码：<strong><?php echo $this->getCategory('audio_encode', $torrent['audio_encode'])?></strong>
            	分辨率：<strong><?php echo $this->getCategory('resolution', $torrent['resolution'])?></strong>
            	制作组：<strong><?php echo $this->getCategory('team', $torrent['team'])?></strong>
            </td>
          </tr>
          
          <tr>
            <td class="hidden-sm hidden-xs">行为</td>
            <td><a href="javascript:;" class="text-danger">删除种子</a><a href="<?php echo framework\App::ins()->user->getId() == $torrent['user_id'] ? $this->createUrl('torrent/edit', array('id' => $torrent['id'])) : '#'?>" class="text-primary">编辑种子</a><a href="javascript:;" class="text-warning">举报种子</a></td>
          </tr>
          
          <?php if (!empty($torrent['douban_id'])):?>
          <tr>
            <td class="hidden-sm hidden-xs">豆瓣信息</td>
            <td id="douban_info" data-douban-id="<?php echo $torrent['douban_id']?>"></td>
          </tr>
          <?php endIf?>
          
          <tr>
            <td class="hidden-sm hidden-xs">简介</td>
            <td>
              <div id="introduce"><?php echo $torrent['introduce']?></div>
            </td>
          </tr>
          
          <tr>
            <td class="hidden-sm hidden-xs">种子信息</td>
            <td>
            	hash码：<span class="text-primary"><?php echo $torrent['info_hash']?></span>
            	<span><a href="javascript:;" id="view-filelist" data-show="false">[ <span class="view-action">查看</span>文件(<?php echo $torrent['file_count']?>) ]</a></span>
            	<?php echo $fileList?>
            </td>
          </tr>
          <tr>
            <td class="hidden-sm hidden-xs">热度表</td>
            <td>查看：<span class="text-primary"><?php echo $torrent['view_times']?>次</span>下载：<span class="text-primary"><?php echo $torrent['download_times']?>次</span>完成：<span class="text-primary"><?php echo $torrent['finish_times']?>次</span><span id="view-snatch"><a href="<?php echo $this->createUrl('torrent/snatch', array('id' => $torrent['id']))?>">[查看完成情况]</a></span></td>
          </tr>
          <tr>
            <td class="hidden-sm hidden-xs">同伴</td>
            <td>
            	<button class="btn btn-xs btn-info" id="partner"  data-haved="false">查看小伙伴们</button>做种者：<span class="text-primary"><em class="seeder-count"><?php echo $torrent['seeder_count']?></em>个</span>下载者：<span class="text-primary "><em class="leecher-count"><?php echo $torrent['leecher_count']?></em>个</span>
            	<div id="seeder-leecher-list" style="display: none">
            	
            	</div>
            </td>
          </tr>
          <tr>
            <td class="hidden-sm hidden-xs">魔力奖励</td>
            <td>
            	目前发布者已收到奖励：<strong class="award-total"><?php echo $userAwardSum?></strong>，有以下会员给予了奖励：
            	<p class="award-list">
            	<?php echo $userAward?>
            	</p>
            	<button class="btn btn-success btn-xs award" data-value="100" data-type="2" title="从自己魔力扣除100奖励发布者" data-toggle="popover" data-placement="top" data-trigger="manual">100</button>
            	<button class="btn btn-success btn-xs award" data-value="200" data-type="2" title="从自己魔力扣除200奖励发布者" data-toggle="popover" data-placement="top" data-trigger="manual">200</button>
            	<button class="btn btn-success btn-xs award" data-value="500" data-type="2" title="从自己魔力扣除500奖励发布者" data-toggle="popover" data-placement="top" data-trigger="manual">500</button>
            	<button class="btn btn-success btn-xs award" data-value="1000" data-type="2" title="从自己魔力扣除1000奖励发布者" data-toggle="popover" data-placement="top" data-trigger="manual">1000</button>
            </td>
          </tr>
          <tr>
            <td class="hidden-sm hidden-xs">感谢者</td>
            <td>
            	<p class="award-list">
            		<?php echo $systemAward?>
            	</p>
            	<button class="btn btn-default btn-xs award" data-value="3"  data-type="1" data-toggle="popover" data-placement="top" data-trigger="manual">点击感谢发布者，由系统奖励发布者3魔力</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div><!-- 评论详情表格结束 -->

    
    <!-- 添加评论框 -->
	 <div class="row comment-add">
    	<div class="col-md-offset-3 col-md-6">
    		<textarea maxLength="200"></textarea>
    	</div>
    	<div class="col-md-offset-3 col-md-6 text-center" style="position: relative">
    		<button class="btn btn-primary" id="add-building">提交评论</button>
    	</div>
    </div>
   

    
    <input type="hidden" id="torrentId" value="<?php echo $torrent['id']?>">
    <input type="hidden" id="baseUrl" value="<?php echo framework\App::ins()->request->getBaseUrl()?>">
    <input type="hidden" id="username" value="<?php echo framework\App::ins()->user->getName()?>">
    <input type="hidden" id="getSeederLeecherUrl" value="<?php echo $this->createUrl('torrent/getSeederLeecher')?>">
    <input type="hidden" id="user-profile-baseurl" value="">
    <input type="hidden" id="award-url" value="<?php echo $this->createUrl('torrent/addaward')?>">
    
<div id="seeder-leecher-list-tpl" style="display: none;">
	<table class="table table-hover">
		<caption>做种者</caption>
		<thead>
			<tr>
				<th>用户名</th>
				<th>可连接</th>
				<th>上传量</th>
				<th>即时速度</th>
				<th>下载量</th>
				<th>即时速度</th>
				<th>分享率</th>
				<th>完成</th>
				<th>连接时间</th>
				<th>最近汇报</th>
				<th>客户端</th>
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
</div>
 <script src="<?php echo framework\App::ins()->request->getBaseUrl()?>application/assets/lib/ueditor/ueditor.parse.min.js"></script>
 <script src="<?php echo framework\App::ins()->request->getBaseUrl()?>application/assets/js/torrent_detail.js"></script>
