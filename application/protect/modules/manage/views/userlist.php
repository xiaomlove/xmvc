<h3 class="main-title">
	<strong>用户列表</strong>
	<input type="text" class="user-search"><span class="glyphicon glyphicon-search" aria-hidden="true" title="搜索用户"></span>
	<a class="btn btn-primary pull-right" href="<?php echo $this->createUrl('manage/user/useradd')?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>添加用户</a>
</h3>
<div id="user-table">
<?php include 'usertable.php'?>
</div>
<script>
	var $userTable = $("#user-table");
	$userTable.on("click", ".pagination li", function(e){
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
// 		console.log(page);return;
		$.ajax({
			url: "<?php echo $this->createUrl('manage/user/userlist')?>",
			type: "GET",
			dataType: "json",
			data: "page=" + page + "&ajax=true",
		}).done(function(result){
			if (result.code == 1) {
// 				console.log(result.data);
				$userTable.html(result.data);
			}
		})
	})
</script>