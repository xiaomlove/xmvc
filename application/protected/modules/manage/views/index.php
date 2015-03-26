<h1 class="page-header"><strong>欢迎来到管理首页，点击左边菜单开始</strong></h1>

<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered table-hover">
			<tr>
				<td>服务器时间：</td>
				<td><?php echo date("Y-m-d l H:i:s A")?></td>
			</tr>
			<tr>
				<td>服务器IP：</td>
				<td><?php echo gethostbyname($_SERVER['SERVER_NAME'])?></td>
			</tr>
			<tr>
				<td>服务器操作系统：</td>
				<td><?php echo php_uname()?></td>
			</tr>
			<tr>
				<td>服务器软件信息：</td>
				<td><?php echo $_SERVER['SERVER_SOFTWARE']?></td>
			</tr>
			<tr>
				<td>PHP版本：</td>
				<td><?php echo PHP_VERSION?></td>
			</tr>
			<tr>
				<td>PHP运行方式：</td>
				<td><?php echo php_sapi_name()?></td>
			</tr>
			<tr>
				<td>数据库信息：</td>
				<td><?php echo $db->serverInfo?></td>
			</tr>
			<tr>
				<td>数据库版本：</td>
				<td><?php echo $db->serverVersion?></td>
			</tr>
			<tr>
				<td>数据库客户端：</td>
				<td><?php echo $db->clientVersion?></td>
			</tr>
			
		</table>
	</div>
</div>