<?php
return array(
	'database'=>array(
		'connectionString'=>'mysql:host=115.28.132.38;dbname=tinypt',
		'username'=>'root',
		'password'=>'123456',
		'charset'=>'utf8',
		'tablePrefix'=>'',			
	),
	
	'router'=>array(
		'mode'=>'path',//有path(index.php/user/login),normal(index.php?c=user&a=login),默认normal
		'showScriptName'=>FALSE,
		'ruleSeparator'=>'/',//规则分割符，默认/，可以换-等，换了rules里边也得换，要一致
		//rules配置的左边是浏览器看到的，是域名后面不包含入口文件(如果显示入口文件)和?a=1&b=3的部分
		'rules'=>array(
			'talk'=>'index/talk',
			'index.html'=>'index/home',//如果不传参，把后缀名写到左边全部匹配，减轻匹配工作量，不用数组传了
			'register.html'=>'index/register',
			'login.html'=>'index/login',
			'torrents'=>'torrent/list',
			'upload.html'=>'torrent/upload',
			'detail'=>'torrent/detail',
			'checkregister'=>'index/checkRegister',
			'checklogin'=>'index/checkLogin',
			'logout'=>'index/logout',
			'getipinfo'=>'index/getIpInfo',
			'ueditor'=>'index/ueditor',
			'torrent/edit'=>'torrent/edit',
			'snatch' => 'torrent/snatch',
			'getSeederLeecher'=>'torrent/getSeederLeecher',
			'error'=>'index/error',
			'download'=>'torrent/download',
			'comment/add'=>'comment/add',
			'comment/list'=>'comment/list',
			'addbookmark'=>'bookmark/add',
			'profile'=>'user/profile',
			'about'=>'index/about',
			'user/name/<name:.+>/age/<age:\d+>'=>'user/filter',
			'user/sex/<sex:.+>'=>'user/filter',
			'user/year/<year>'=>array('user/filter', 'urlSuffix'=>'.shtml'),
			'thread/<tid>'=>array('thread/show', 'urlSuffix'=>'.html'),
			
			//论坛模块
			'forum'=>'forum/section/list',
			'forum/thread/list'=>'forum/thread/list',
			'forum/thread/add'=>'forum/thread/add',
			'forum/thread/detail'=>'forum/thread/detail',
			'forum/thread/edit'=>'forum/thread/edit',
			'forum/reply/add'=>'forum/reply/add',
			'forum/reply/edit'=>'forum/reply/edit',
			'forum/thread/addview'=>'forum/thread/addview',
			'forum/thread/addappraise'=>'forum/thread/addappraise',
			'forum/replyreply/add'=>'forum/replyreply/add',
			'forum/replyreply/list'=>'forum/replyreply/list',
	
			//管理模块
			'admin'=>'manage/index/index',
			'admin/forum/section'=>'manage/forum/sectionlist',
			'admin/forum/section/add'=>'manage/forum/sectionadd',
			'admin/forum/section/edit'=>'manage/forum/sectionedit',
			'admin/user'=>'manage/user/userlist',
			'admin/user/add'=>'manage/user/useradd',
			'admin/role'=>'manage/role/rolelist',
			'admin/role/add'=>'manage/role/roleadd',
			'admin/role/edit'=>'manage/role/roleedit',
			'admin/role/addrule'=>'manage/role/addrule',
			'admin/forumset' => 'manage/option/forumset',
			'admin/rule/list'=>'manage/rule/list',
			'admin/rule/add'=>'manage/rule/add',
			'admin/rule/edit'=>'manage/rule/edit',
			'admin/rule/delete'=>'mamage/rule/delete',
		),
	),
	
	'defaultController'=>'index',
	'defaultAction'=>'home',
	'defaultModule'=>'',
	
	
	//对组件的一些配置
	'component'=>array(
		'user'=>array(
			'guestName'=>'游客',//未登陆时用户名
			'sessionExpire'=>108000,//session有效时间
		)
	),

	'cache'=>array(
		'queryCache'=>FALSE,//不开启查询查询
		'pageCache'=>FALSE,//开启页面缓存
		
		'pageCacheOptions'=>array(
			'expire'=>600,//过期时间
			'path'=>'application.runtime',//缓存保存路径，路径别名,application表示网站根目录
			'rules'=>array(
				array('url'=>'*', 'expire'=>60),//以url为标识缓存所有网页，可以覆盖上边的expire和path
				array('controller'=>'index', 'action'=>'index'),//以module,controller,action,param为标识的，不能有url
			)
		
		),
		//以下是配置，如果添加时有传递，以传递的为准
		'queryCacheOptions'=>array(
			'expire'=>600,
			'path'=>'application.runtime.query',//查询缓存就不用规则了，是否缓存写到代码里边	
		)
		
	),
	
	'session'=>array(
		'gc_maxlifetime'=>'',//gc发生时间，感觉这块没必要，默认即可
	),
		
	'torrentSavePath'=>'application.protected.data.torrents',//种子保存目录
	
);