<h3 class="main-title" id="main-title">
	<strong>用户列表</strong>
	<input type="text" class="user-search"><span class="glyphicon glyphicon-search" aria-hidden="true" title="搜索用户"></span><small class="clear-keyword"><a href="javascript:;">重置条件</a></small>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/user/useradd')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加用户</a>
</h3>

<table class="table table-bordered table-hover user-list-table">
	<thead id="user-thead">
	<tr>
		<th data-field="id">用户ID</th>
		<th data-field="name">用户名</th>
		<th data-field="email">邮箱</th>
		<th data-field="uploaded">上传量</th>
		<th data-field="downloaded">下载量</th>
		<th data-field="last_login_time">上次登陆</th>
		<th>角色</th>
		<th>操作</th>
	</tr>
	</thead>
	<tbody id="user-tbody">
	<?php include 'usertable.php'?>
	</tbody>
</table>

<nav style="text-align:center" id="pagination">
  <?php echo $pagination?>
</nav>

<script>
	var $userTbody = $("#user-tbody"), $pagination = $("#pagination"), $userThead = $("#user-thead");
	var filter = {field: "id", type: "ASC", "page": 1, "ajax": "true"};
	$pagination.on("click", ".pagination li", function(e){
		if ($(this).hasClass("disabled")) {
			return;
		}
		var activePage = $(this).parent().find(".active").find("span").text();

		var page;
		var $clickA = $(this).find("a");
		if ($clickA.hasClass("prev")) {
			page = parseInt(activePage) - 1;
		} else if ($clickA.hasClass("next")) {
			page = parseInt(activePage) + 1;
		} else {
			page = $clickA.find("span").text().replace(/\./gi, "");//去掉...
		}
		filter.page = page;
// 		console.log(page);return;
		$.when(getData()).done(function(result){
			if (result.code == 1) {
// 				console.log(result.data);
				$userTbody.html(result.data.tbody);
				$pagination.html(result.data.pagination);
			}
		})
	})
	
	//表头点击排序
	$userThead.on("click", "th[data-field]", function(e){
		var field = $(this).attr("data-field");
		var $th = $(this);
		var changeTh = false;
		if (field != filter.field) {
			//先前的排序字段不是点击的这个，则按DESC排
			changeTh = true;
			filter.field = field;
			filter.type = "DESC";
		} else {
			filter.field = field;
			if (filter.type == "ASC") {
				filter.type = "DESC";
			} else {
				filter.type = "ASC";
			}
		}
		filter.page = 1;
		$.when(getData()).done(function(result){
			if (result.code == 1) {
				$userTbody.html(result.data.tbody);
				$pagination.html(result.data.pagination);
				if (filter.type == "DESC") {
					var direction = "down";
				} else {
					var direction = "up";
				}
				if (changeTh) {
					$userThead.find("th[data-sorted]").removeAttr("data-sorted").find("span").remove();
					$th.attr("data-sorted", "true").append('<span class="glyphicon glyphicon-arrow-' + direction + '" aria-hidden="true"></span>');
				} else {
					$th.find("span").remove().end().append('<span class="glyphicon glyphicon-arrow-' + direction + '" aria-hidden="true"></span>');
				}
				$th.attr("data-sorted", "true");
			}
		})
	})
	
	function getData() {
		var dfd = $.Deferred();
		$.ajax({
			url: "<?php echo $this->createUrl('manage/user/userlist')?>",
			type: "GET",
			dataType: "json",
			data: filter,
		}).done(function(result){
			if (filter.keyword) {
				$searchInput.val(filter.keyword);
			} else {
				$searchInput.val("");
			}
			dfd.resolve(result);
		});
		return dfd.promise();
	}

	//搜索相关
	var $mainTitle = $("#main-title");
	var $searchInput = $mainTitle.find(".user-search");
	var $searchBtn = $mainTitle.find(".glyphicon-search");
	var $resetBtn = $mainTitle.find(".clear-keyword");
	
	$searchBtn.on("click", function(e){
		if ($searchInput.val().trim() == "") {
			alert("请输入关键字");
			return;
		}
		filter.keyword = encodeURIComponent($searchInput.val().trim());
		resetFilter();
		$.when(getData()).done(function(result){
			if (result.code == 1) {
				$userTbody.html(result.data.tbody);
				$pagination.html(result.data.pagination);
			}
		})
		
	})
	
	function resetFilter() {
		filter.page = 1;
		filter.field = "id";
		filter.type = "ASC";
		$userThead.find("th[data-sorted]").removeAttr("data-sorted").find("span").remove();
	}

	$resetBtn.on("click", function(e){
		resetFilter();
		delete filter.keyword;
		$.when(getData()).done(function(result){
			if (result.code == 1) {
				$userTbody.html(result.data.tbody);
				$pagination.html(result.data.pagination);
			}
		})
		
	})
	
</script>