	<?php echo $this->getSearchBox()?>

<div class="row">
      <table class="table table-bordered table-hover table-striped" id="torrent-list-table">
        <thead>
          <tr>
            <th style="text-align: center"><?php echo $this->getSortHref('main_title', '标题')?></th>
            <th class="hidden-sm hidden-xs"><?php echo $this->getSortHref('add_time', '添加于')?></th>
            <th class="hidden-sm hidden-xs" title="存活时间">TTL</th>
            <th class="hidden-xs"><?php echo $this->getSortHref('size', '大小')?></th>
            <th class="hidden-xs"><?php echo $this->getSortHref('seeder_count', '做种')?></th>
            <th class="hidden-xs"><?php echo $this->getSortHref('leecher_count', '下载')?></th>
            <th class="hidden-md hidden-sm hidden-xs"><?php echo $this->getSortHref('finish_times', '完成')?></th>
            <th class="hidden-md hidden-sm hidden-xs"><?php echo $this->getSortHref('comment_count', '评论')?></th>
            <th class="hidden-md hidden-sm hidden-xs"><?php echo $this->getSortHref('view_times', '查看')?></th>
            <th class="hidden-xs"><?php echo $this->getSortHref('user_name', '发布者')?></th>
          </tr>
        </thead>
        <?php if(!empty($data)):?>
        <tbody>
        <?php foreach ($data as $k => $v):?>
          <tr>
            <td><a href="<?php echo $this->createUrl('torrent/detail', array('id' => $v['id']))?>"><?php echo $v['main_title']."<br/>".$v['slave_title']?></a></td>
            <td class="hidden-sm hidden-xs"><?php echo date('Y-m-d', $v['add_time'])."<br/>".date('H:i:s', $v['add_time'])?></td>
            <td class="hidden-sm hidden-xs"><?php echo $this->getTTL($v['add_time'], '<br/>')?></td>
            <td class="hidden-xs"><?php echo $this->getSize($v['size'])?></td>
            <td title="做种" class="hidden-xs"><?php echo $v['seeder_count']?></td>
            <td title="下载" class="hidden-xs"><?php echo $v['leecher_count']?></td>
            <td title="完成" class="hidden-md hidden-sm hidden-xs"><?php echo $v['finish_times']?></td>
            <td title="评论" class="hidden-md hidden-sm hidden-xs"><?php echo $v['comment_count']?></td>
            <td title="查看" class="hidden-md hidden-sm hidden-xs"><?php echo $v['view_times']?></td>
            <td class="hidden-xs"><a href="#"><?php echo $v['user_name']?></a></td>
          </tr>
          <?php endForeach?>
        </tbody>
        <?php endIf?>
      </table>
      <nav id="torrent-list-nav">
        <?php echo $navHtml?>
      </nav>
    </div>
    <input type="hidden" id="torrent-url" value="<?php echo $this->createUrl('torrent/list')?>" />
 <script>
 	//收缩展开搜索箱
	var $categoryBox = $('.category-box');
	$('.search-box-icon').click(function(e) {
		if ($categoryBox.hasClass('hidden')) {
			$(this).children('span').attr('class', 'glyphicon glyphicon-minus-sign');
			$categoryBox.removeClass('hidden');
		} else {
			$(this).children('span').attr('class', 'glyphicon glyphicon-plus-sign');
			$categoryBox.addClass('hidden');
		}
	});
	//切换全选、全不选
	$('.select-all').click(function(e) {
		var $btn = $(this);
		if ($btn.attr('data-selected') === 'true') {
			$btn.removeAttr('data-selected').text('全选').parents('.category-item').find('.sub-category input[type=checkbox]').prop('checked', false);
		} else {
			$btn.attr('data-selected', 'true').text('全不选').parents('.category-item').find('.sub-category input[type=checkbox]').prop('checked', true);
		}
	});
	//防止点击某一分类选中文字
	$categoryBox.on('mousedown', 'label', function(e) {
		e.preventDefault();
		e.stopPropagation();
// 		if (window.getSelection) {
// 			window.getSelection().removeAllRanges();
// 		} else if (document.selection) {
// 			document.selection.empty();
// 		}
	});
	//点击“给我搜”
	var $searchBtn = $('#search-btn');
	$searchBtn.click(function(e) {
		//分类部分
		var category = '';
		$categoryBox.find('.category-item').each(function(index, item) {
			var $item = $(this);
			var field = $item.attr('data-field');
			var $checked = $item.find('input[name=' + field + ']:checked');
			if ($checked.length) {
				category += field + '=' + $checked.map(function() {
					return $(this).val();
				}).get().join(',') + '&';
			}
		});
		
		//关键字部分
		var keyword = $.trim($('input[name=keyword]').val());
		if (keyword !== '') {
			keyword = 'keyword=' + keyword + '&range=' + $('select[name=range]').val() + '&';
		}
		//右边存活、促销状态
		var activeState = $categoryBox.find('select[name="active-state"]').val();
		var spState = $categoryBox.find('select[name="sp-state"]').val();

		//拼凑最终url
		var params = '?';
		if (category !== '') {
			params += category;
		}
		if (keyword !== '') {
			params += keyword
		}
		if (activeState > 0) {
			params += 'active_state=' + activeState + '&';
		}
		if (spState > 0) {
			params += 'sp_state=' + spState + '&';
		}
		if (params === '?') {
			return;
		} else {
			params = params.substring(0, params.length - 1);
		}
// 		console.log(params);return;
		window.location.href= $('#torrent-url').val() + params;		
	})
 </script>