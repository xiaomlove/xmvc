<?php
/**
 * 为了速度，面向过程一步一步的来。
 */
//echo '<pre/>';
//var_dump(strlen($_GET['peer_id']));
//var_dump($_SERVER);exit;

//define('MODE', 'RELEASE');

//-1，如果测试模式，输出错误
define('DEBUG', TRUE);
if (defined('DEBUG') && DEBUG)
{
	error_reporting(E_ALL | E_STRICT);//所有错误都报告
	ini_set('display_errors', 'Off');
	ini_set('log_errors', 'On');
	ini_set('error_log', 'announce_error_log');
}
//0、引入必须的辅助函数文件，里边引入了必须的BEncode类
//记录一个tracker请求需要的时间
define('START', microtime(true));
$fopen = fopen('sql_log', 'a');
fwrite($fopen, '***************************BEGIN***'.START.'*******************************'."\r\n");
fclose($fopen);
unset($fopen);


define('TIMENOW', $_SERVER['REQUEST_TIME']);
require 'framework/lib/announce_functions.php';

//1、检查参数是否齐全以及合法，$_GET传递过来的都是字符串类型
$fo = fopen('get', 'a');
fwrite($fo, serialize($_GET));
fclose($fo);
unset($fo);
if (!isset($_GET['passkey']) || strlen($_GET['passkey']) !== 32)
{
	error('error passkey');
}
if (!isset($_GET['info_hash']) || strlen($_GET['info_hash']) !== 20)//是原始sha1值20字节，传送时经过了urlencode，php接收到又自动urldecode回20字节的原始sha1值
{
	error('error info_hash');
}
if (!isset($_GET['peer_id']) || strlen($_GET['peer_id']) !== 20)//同上
{
	error('error peer_id');
}
$_GET['peer_id'] = urlencode($_GET['peer_id']);//encode回传送时的值，info_hash需要的是16进制版，不需要传递时的
if (!isset($_GET['port']) || !ctype_digit($_GET['port']) || intval($_GET['port']) > 65535)//端口范围一般多少？
{
	error('error port');
}
if (!isset($_GET['uploaded']) || !ctype_digit($_GET['uploaded']) || intval($_GET['uploaded']) < 0)
{
	error('error uploaded');
}
if (!isset($_GET['downloaded']) || !ctype_digit($_GET['downloaded']) || intval($_GET['downloaded']) < 0)
{
	error('error downloaded');
}
if (!isset($_GET['left']) || !ctype_digit($_GET['left']) || intval($_GET['left']) < 0)
{
	error('error left');
}
if (!isset($_GET['compact']) || !ctype_digit($_GET['compact']) || intval($_GET['compact']) < 0 || intval($_GET['compact']) > 1)//不是0就是1
{
	error('error compact');
}
if (isset($_GET['no_peer_id']))
{
	//如果传递，有可能不传，一般有了compact这个不会有
	if (!ctype_digit($_GET['no_peer_id']) ||  intval($_GET['no_peer_id']) < 0 || intval($_GET['no_peer_id']) > 1)
	{
		error('error no_peer_id');
	}
}
if (isset($_GET['event']))
{
	//如果有事件，必须是started,stoped,completed
	$eventAllow = array('started', 'stopped', 'completed');
	if (!in_array($_GET['event'], $eventAllow))
	{
		error('error event');
	}
}
if (isset($_GET['numwant']))
{
	//客户端想要获得的peers数量，如果没有默认为50
	if (!ctype_digit($_GET['numwant']))
	{
		error('error numwant');
	}
}
else 
{
	$_GET['numwant'] = 50;
}
/*--------------------------典型参数检测完毕-----------------------*/

//2、检测是否浏览器访问以及是否GET方式
$agent = denyBrowser();

//3、检测ip地址，无法获取ip直接退出
$ip = 'unknown';
if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
{
	$ip = getenv('HTTP_CLIENT_IP');
}
elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
{
	$ip = getenv('HTTP_X_FORWARDED_FOR');
}
elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
{
	$ip = $_SERVER['REMOTE_ADDR'];
}
if (!strcasecmp($ip, 'unknown'))
{
	error('ip unkown');
}
if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE)
{
	error('not ipv4 address');//只考虑ipv4
}

//4、常规检测完毕，引入配置文件，连接数据库，定义增删改、查函数
if (defined('MODE') && MODE === 'RELEASE')
{
	$config = require 'application/protected/config/config-release.php';
}
else
{
	$config = require 'application/protected/config/config.php';
}
$pdo = connectDB($config);

//5、验证passkey，获得个人信息
$sql = 'SELECT id, downloaded, uploaded, seed_time, leech_time, is_banned, is_hang_up, use_banned_client FROM user WHERE passkey=:passkey LIMIT 1';
$userInfo = query($sql, array(':passkey' => $_GET['passkey']));
if (empty($userInfo))
{
	error('passkey error');
}
$userInfo = $userInfo[0];
if ($userInfo['is_banned'])
{
	error('user is banned');//用户已被BAN
}
if ($userInfo['is_hang_up'])
{
	error('user is hang up');//用户账号已经挂起
}


//6、检测是否许可的bt客户端请求，简单判断一下，版本先不判断。这些应该存到数据库已方便更改，但要查数据库又慢了
$clientAllow = array('uTorrent', 'Transmission', 'Azureus', 'BitTorrent', 'rTorrent', 'BitRocket');
$isClientAllow = FALSE;
foreach ($clientAllow as $client)
{
	if (stripos($agent, $client) !== FALSE)
	{
		$isClientAllow = TRUE;
		break;
	}
}
unset($client);
if (!$isClientAllow)
{
	$sql = 'UPDATE user SET use_banned_client = 1 WHERE user_id='.$userInfo['id'];//标记使用了非法客户端
	execute($sql);
	error('not allow client');
}
if ($userInfo['use_banned_client'])
{
	//被标记了使用非法各客户端，本次正常，去掉标记
	$sql = 'UPDATE user set use_banned_client=0 WHERE user_id='.$userInfo['id'];
	execute($sql);
}

//7、根据info_hash取出种子信息
$unpack = unpack('H*', $_GET['info_hash']);//转化为数据库存的40位的16进制版本，这里$_GET['info_hash']已经自动urldecode过
$info_hash = $unpack[1];
$sql = 'SELECT id, size, add_time, seeder_count, leecher_count, user_id, is_deleted FROM torrent WHERE info_hash=:info_hash LIMIT 1';
$torrent = query($sql, array(':info_hash' => $info_hash));
if (empty($torrent))
{
	error('torrent not exist');
}
$torrent = $torrent[0];
if ($torrent['is_deleted'])
{
	error('torrent is deleted');
}

//8、取peer信息
//判断是否做种者
if (intval($_GET['left']) === 0 )
{
	$isSeeder = TRUE;//标记为做种者。
}
else 
{
	$isSeeder = FALSE;
}

$torrentPeerNum = intval($torrent['seeder_count']) + intval($torrent['leecher_count']);//种子中记录的peers数量
$sql = 'SELECT is_seeder, peer_id, ip, port, downloaded, uploaded, `left`, start_time, this_report_time, connect_time FROM peer WHERE torrent_id=:torrent_id';
$peerSelf = query($sql.' AND user_id=:user_id LIMIT 1', array(':torrent_id' => $torrent['id'], ':user_id' => $userInfo['id']));
if (empty($peerSelf))
{
	$isFirstRequest = TRUE;//是客户端第一次请求，peer表中没有记录
	$peerSelf = NULL;
}
else 
{
	$isFirstRequest = FALSE;
	$peerSelf = $peerSelf[0];
	//不是第一次请求，看一下时间间隔是否太小
	if (TIMENOW - $peerSelf['this_report_time'] < 30)
	{
		if (!isset($_GET['event']) || $_GET['event'] === 'started')//stopped和completed不应该限制
		{
			error('min interval 30s');
		}
		
	}
	//再看是否重复下载：peer_id跟数据库不相同又不是做种者。感觉有时任务删除没能同步过来把peer删除，会有点问题。所以加上时间，如果已有
	//peer 24小时不活动，就不算重复了。下边插入时直接更新原有peer。没法考虑如此多，peer_id停止再开始会变
	if ($peerSelf['peer_id'] != $_GET['peer_id'] && !$isSeeder)
	{
		if ($peerSelf['left'] == 0)
		{
			error('already seeding this torrent');
		}
		else 
		{
			error('already downloading this torrent');
		}
		
	}
	if ($isSeeder)
	{
		//即使做种，也不能同时多于3处做
		$checkPlacessql = 'SELECT count(*) as count FROM peer WHERE user_id=:user_id AND torrent_id=:torrent_id';
		$seedPlaces = query($checkPlacessql, array(':user_id' => $userInfo['id'], ':torrent_id' => $torrent['id']));
		if (!empty($seedPlaces) && $seedPlaces[0]['count'] > 3)
		{
			error('do not seed one torrent more than 3 places');
		}
	}
}

$peerList = query($sql.' AND user_id<>'.$userInfo['id'], array(':torrent_id' => $torrent['id']));//所有该种子的peer列表，不包括自己
if (intval($_GET['numwant']) < count($peerList))
{
	//渴望得到的数量要小于查询出来的，截取之
	shuffle($peerList);
	$returnPeerList = array_slice($peerList, 0, $_GET['numwant']);
}
else 
{
	$returnPeerList = $peerList;
}

//9、拼凑返回信息
$return = array('isDict' => TRUE);

$interval = 300;//1800;//默认常规请求间隔，30分钟
if (TIMENOW - $torrent['add_time'] > 3600*24*7)
{
	$interval = 3600;//发布7天后的种子，1小时请求一次。不分那么多了
}
$return['interval'] = $interval;
$return['min interval'] = 30;//最小时间间隔为30秒
$return['complete'] = intval($torrent['seeder_count']);
$return['incomplete'] = intval($torrent['leecher_count']);

if ($_GET['compact'])
{
	$return['peers'] = '';//二进制模式，每个peer是一个6字节的字符串。同样不需要peer_id
	foreach ($returnPeerList as $peer)
	{
		if (filter_var($peer['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		{
			//这个二进制模式只能是ipv4？至于这个pack，参考自nexusphp，这么输出就是6字节的字符串了
			$return['peers'] .= pack('Nn', ip2long($peer['ip']), $peer['port']);
		}
	}
}
else 
{
	$return['peers'] = array();//字典模式，每个peer是一个字典
	foreach ($returnPeerList as $peer)
	{
		if ($_GET['no_peer_id'])
		{
			//不需要peer_id
			$return['peer'][] = array(
				'isDict' => TRUE,
				'ip' => $peer['ip'],
				'port' => intval($peer['port'])//这个是整型
			);
		}
		else 
		{
			$return['peers'][] = array(
				'isDict' => TRUE,
				'peer id' => $peer['peer_id'],
				'ip' => $peer['ip'],
				'port' => intval($peer['port'])
			);
		}
	}
}
unset($peer);
$returnDict = BEncode::encode($return);
if (empty($returnDict))
{
	trigger_error('announce encode error', E_USER_ERROR);
	exit();
}

//10、返回的数据拼凑完毕。检查是否作弊
if (!$isFirstRequest)
{
	$uploadThis = max(0, ($_GET['uploaded'] - $peerSelf['uploaded']));
	$downloadThis = max(0, ($_GET['downloaded'] - $peerSelf['downloaded']));
	$duration = TIMENOW - $peerSelf['this_report_time'];
	$uploadSpeed = $uploadThis/$duration;//上传速度，存入时保留字节/S
	$downloadSpeed = $downloadThis/$duration;
	if ($uploadSpeed/1024/1024 > 100)
	{
		//速度超100MB/s，算作弊了，也只能从速度随便判断一下了
		$sql = 'UPDATE user SET is_banned=1 WHERE user_id='.$userInfo['id'];
		execute($sql);
		error('you are cheating,we will disabled your account');
	}
}
else
{
	//没有记录，一定是started发生时
	$uploadThis = 0;
	$downloadThis = 0;
	$duration = 0;
	$uploadSpeed = 0;
	$downloadSpeed = 0;
}

//11、计算优惠之类的，暂时无



//12、处理event事件，更新user，torrent，peer，snatch数据
$debug = array();
$debug['agent'] = $agent;
$debug['uploaded'] = $_GET['uploaded'];
$debug['downloaded'] = $_GET['downloaded'];
$debug['left'] = $_GET['left'];
if (isset($_GET['event']))
{
	$updateTorrentSql = "UPDATE torrent SET ";//更新torrent数据
	switch ($_GET['event'])
	{
		case 'stopped'://停止一个任务、删除一个任务或者退出客户端，会有stopped事件，暂停不会触发
			$sql = 'DELETE FROM peer WHERE user_id='.$userInfo['id'].' AND torrent_id='.$torrent['id'];//peer_id会变
			execute($sql);//删除该peer
			$debug['event'] = 'stopped';
			$debug['sql'][] = $sql;
// 			$sql = 'DELETE FROM snatch WHERE user_id='.$userInfo['id'].' AND torrent_id='.$torrent['id'].' AND complete_time=0';
// 			execute($sql);//删除事先插入的未完成snatch
			if ($isSeeder)
			{
				$updateTorrentSql .= "seeder_count=seeder_count-1";//种子的peer数量减1
			}
			else
			{
				$updateTorrentSql .= "leecher_count=leecher_count-1";
			}
			break;
		case 'completed'://下载完成会友触发
 			//$sql = 'UPDATE snatch SET complete_time='.TIMENOW.',is_seeder=1 WHERE user_id='.$userInfo['id'].' AND torrent_id='.$torrent['id'].' AND complete_time=0';
			//execute($sql);
			$debug['event'] = 'completed';
 			$isCompleted = TRUE;//完成标记，后面连接到更新的字段中
			$updateTorrentSql .= "finish_times=finish_times+1,seeder_count=seeder_count+1,leecher_count=leecher_count-1";//种子完成数加，做种数加1，下载数减1
			break;
		case 'started'://新建一个任务或者任务由停止到开始会触发，暂停开始不会
			if ($isSeeder)
			{
				$updateTorrentSql .= "seeder_count=seeder_count+1";//种子的peer数量加1
			}
			else
			{
				$updateTorrentSql .= "leecher_count=leecher_count+1";
			}
			$debug['event'] = 'started';
			$isStarted = TRUE;
			break;//插入peer和snatch在下边
		default:
			$debug['event'] = '';
			break;
	}
	$updateTorrentSql .= " WHERE id=".$torrent['id'];
	execute($updateTorrentSql);
	$debug['sql'][] = $updateTorrentSql;
}
//判断用户是否可连接
$connectable = fsockopen($ip, $_GET['port'], $errno, $errstr, 1);
if ($connectable === FALSE)
{
	$connectable = 0;
}
else 
{
	$connectable = 1;
}
if (isset($_GET['event']) && $_GET['event'] === 'stopped')
{
	$sql = 'SELECT count(*) as count FROM peer WHERE user_id=:user_id';
	$userPeerCount = query($sql, array(':user_id' => $userInfo['id']));
	if ($userPeerCount[0]['count'] == 0)
	{
		$connectable = 2;//已经没有下载着的任务，为默认的2，表示“未知”。
	}
}

//更新user数据
$updateUserSql = "UPDATE user SET downloaded=downloaded+$downloadThis, uploaded=uploaded+$uploadThis, ";
if ($isSeeder)
{
	$updateUserSql .= "seed_time=seed_time+$duration";
}
else 
{
	$updateUserSql .= "leech_time=leech_time+$duration";
}
$updateUserSql .= ", connectable=$connectable WHERE id=".$userInfo['id'];
execute($updateUserSql);
$debug['sql'][] = $updateUserSql;
//只要不是stopped（会删除peer）,都要更新peer。peer表和snatch表基本一致，peer多了passkey、is_seeder两个字段而已。UPDATE：保持一致吧，是否完成通过is_seeder判断，对了，多一个complete_time（完成时间）
$isSeeder = (int)$isSeeder;
$timenow = TIMENOW;
//检查是否已经存在 
$checkSnatchSql = 'SELECT * FROM snatch WHERE user_id='.$userInfo['id'].' AND torrent_id='.$torrent['id'].' AND complete_time=0';
$snatch = query($checkSnatchSql);
$debug['sql'][] = $checkSnatchSql;
if (!empty($snatch))
{
	$hasSnatch = TRUE;
	$snatch = $snatch[0];
}

//处理peer数据。没有事件，或者为started要插入，为completed也要更新
if (!isset($_GET['event']) || $_GET['event'] !== 'stopped')
{
	if (isset($isStarted) && $isStarted)
	{
		$sql = 'INSERT INTO peer (torrent_id, torrent_size, peer_id, ip, port, uploaded, downloaded, `left`, is_seeder, start_time, last_report_time, this_report_time, user_id, connectable, agent, passkey, upload_speed, download_speed, connect_time) VALUES (';
		$sql .= "{$torrent['id']}, {$torrent['size']}, '{$_GET['peer_id']}', '$ip', {$_GET['port']}, $uploadThis, $downloadThis, {$_GET['left']}, $isSeeder, $timenow, $timenow, $timenow, {$userInfo['id']}, $connectable, '$agent', '{$_GET['passkey']}', $uploadSpeed, $downloadSpeed, $duration) ";
		$sql .= "ON DUPLICATE KEY UPDATE ip='$ip',port={$_GET['port']},uploaded=$uploadThis,downloaded=$downloadThis,`left`={$_GET['left']},is_seeder=$isSeeder,last_report_time=this_report_time,this_report_time=$timenow,connectable=$connectable,agent='$agent',upload_speed=$uploadSpeed,download_speed=$downloadSpeed,connect_time=$duration";
// 		if (isset($hasSnatch) && $hasSnatch)
// 		{
// 			$sql = 'INSERT INTO peer (torrent_id, torrent_size, peer_id, ip, port, uploaded, downloaded, `left`, is_seeder, start_time, last_report_time, this_report_time, user_id, connectable, agent, passkey, upload_speed, download_speed, connect_time) VALUES (';
// 			$sql .= "{$torrent['id']}, {$torrent['size']}, '{$_GET['peer_id']}', '$ip', {$_GET['port']}, {$snatch['uploaded']}, {$snatch['downloaded']}, {$_GET['left']}, $isSeeder, {$snatch['start_time']}, {$snatch['this_report_time']}, $timenow, {$userInfo['id']}, $connectable, '$agent', '{$_GET['passkey']}', $uploadSpeed, $downloadSpeed, {$snatch['connect_time']})";
// 		}
// 		else
// 		{
			//$sql = 'INSERT INTO peer (torrent_id, torrent_size, peer_id, ip, port, uploaded, downloaded, `left`, is_seeder, start_time, last_report_time, this_report_time, user_id, connectable, agent, passkey, upload_speed, download_speed, connect_time) VALUES (';
			//$sql .= "{$torrent['id']}, {$torrent['size']}, '{$_GET['peer_id']}', '$ip', {$_GET['port']}, $uploadThis, $downloadThis, {$_GET['left']}, $isSeeder, $timenow, $timenow, $timenow, {$userInfo['id']}, $connectable, '$agent', '{$_GET['passkey']}', $uploadSpeed, $downloadSpeed, 0)";
// 		}
		
	}
	else
	{
		//第一次请求，是插入peer，但有可能之前下载过，后面停止或者删除或者退出客户端时由于某种原因没有删除peer。那上边判断是看已存在peer最后活动时间，所以这里有就更新，没有就插入即可
//		$sql = 'INSERT INTO peer (torrent_id, torrent_size, peer_id, ip, port, uploaded, downloaded, `left`, is_seeder, start_time, last_report_time, this_report_time, user_id, connectable, agent, passkey, upload_speed, download_speed, connect_time) VALUES (';
//		$sql .= "{$torrent['id']}, {$torrent['size']}, '{$_GET['peer_id']}', '$ip', {$_GET['port']}, $uploadThis, $downloadThis, {$_GET['left']}, $isSeeder, $timenow, $timenow, $timenow, {$userInfo['id']}, $connectable, '$agent', '{$_GET['passkey']}', $uploadSpeed, $downloadSpeed, $duration) ";
//		$sql .= "ON DUPLICATE KEY UPDATE ip='$ip',port={$_GET['port']},uploaded=uploaded+$uploadThis,downloaded=downloaded+$downloadThis,`left`={$_GET['left']},is_seeder=$isSeeder,last_report_time=this_report_time,this_report_time=$timenow,connectable=$connectable,agent='$agent',upload_speed=$uploadSpeed,download_speed=$downloadSpeed,connect_time=connect_time+$duration";
		$sql = "UPDATE peer SET peer_id='{$_GET['peer_id']}', ip='$ip',port={$_GET['port']},uploaded=uploaded+$uploadThis,downloaded=downloaded+$downloadThis,`left`={$_GET['left']},is_seeder=$isSeeder,last_report_time=this_report_time,this_report_time=$timenow,connectable=$connectable,agent='$agent',upload_speed=$uploadSpeed,download_speed=$downloadSpeed,connect_time=connect_time+$duration";
		$sql .= " WHERE torrent_id={$torrent['id']} AND user_id={$userInfo['id']}";
	}
	execute($sql);
	$debug['sql'][] = $sql;
}


//处理snatch数据。一个peer对应一个snatch，一个snatch可对应多个peer。下载中的peer对应一个complete_time=0的snatch，完成的peer对应同一个complete_time>0的snatch。做种可以有多处。多处做种时更新同一个snatch
$snatchWhere = "WHERE torrent_id={$torrent['id']} AND user_id={$userInfo['id']}";

if (!$isSeeder || ($isSeeder && isset($isCompleted)))//未完成下载前的一次交互（含最后完成那一次）
{
	if (isset($isStarted))
	{
		//未完成。snatch没有记录，是新建任务开始，或者有记录，但是之前删除任务了任务，snatch不会删除，看剩余量是否接近，不接近作废，新建之！
		if (!isset($hasSnatch))
		{
			$sql = 'INSERT INTO snatch (torrent_id, torrent_size, peer_id, ip, port, uploaded, downloaded, `left`, is_seeder, start_time, last_report_time, this_report_time, user_id, connectable, agent, passkey, upload_speed, download_speed, connect_time, complete_time) VALUES (';
			$sql .= "{$torrent['id']}, {$torrent['size']}, '{$_GET['peer_id']}', '$ip', {$_GET['port']}, $uploadThis, $downloadThis, {$_GET['left']}, $isSeeder, $timenow, $timenow, $timenow, {$userInfo['id']}, $connectable, '$agent', '{$_GET['passkey']}', $uploadSpeed, $downloadSpeed, $duration, 0)";
		}
		else 
		{
			if (($_GET['left'] < $snatch['left'] + 1024*128) && ($_GET['left'] > $snatch['left'] - 1024*128))
			{
				//误差假设在128KB之间。有记录，在这个误差中间认为是继续
				$sql = "UPDATE snatch SET peer_id='{$_GET['peer_id']}',ip='$ip',port={$_GET['port']},uploaded=uploaded+$uploadThis,downloaded=downloaded+$downloadThis,`left`={$_GET['left']},is_seeder=$isSeeder,last_report_time=this_report_time,this_report_time=$timenow,connectable=$connectable,agent='$agent',upload_speed=$uploadSpeed,download_speed=$downloadSpeed,connect_time=connect_time+$duration";
				$sql .= " $snatchWhere AND complete_time=0";
			}
			else
			{
				//否则，垃圾snatch数据，删除还是插入
				$delRubbishSnatchSql = "DELETE FROM snatch WHERE id=".$snatch['id'];
				execute($delRubbishSnatchSql);
				$debug['sql'][] = $delRubbishSnatchSql;
				$sql = 'INSERT INTO snatch (torrent_id, torrent_size, peer_id, ip, port, uploaded, downloaded, `left`, is_seeder, start_time, last_report_time, this_report_time, user_id, connectable, agent, passkey, upload_speed, download_speed, connect_time, complete_time) VALUES (';
				$sql .= "{$torrent['id']}, {$torrent['size']}, '{$_GET['peer_id']}', '$ip', {$_GET['port']}, $uploadThis, $downloadThis, {$_GET['left']}, $isSeeder, $timenow, $timenow, $timenow, {$userInfo['id']}, $connectable, '$agent', '{$_GET['passkey']}', $uploadSpeed, $downloadSpeed, $duration, 0)";
			}
		}
		execute($sql);
		$debug['sql'][] = $sql;
	}
	else
	{
		//未完成，这次过后就完成了
		if (isset($isCompleted) && $isCompleted)
		{
			$checkCompleteSnatchSql = "SELECT * FROM snatch $snatchWhere AND is_seeder=1 AND complete_time > 0 LIMIT 1";
			$completeSnatch = query($checkCompleteSnatchSql);
			if (empty($completeSnatch))
			{
				//没有已经完成的,第一次完成
				$sql = "UPDATE snatch SET peer_id='{$_GET['peer_id']}',ip='$ip',port={$_GET['port']},uploaded=uploaded+$uploadThis,downloaded=downloaded+$downloadThis,`left`={$_GET['left']},is_seeder=$isSeeder,last_report_time=this_report_time,this_report_time=$timenow,connectable=$connectable,agent='$agent',upload_speed=$uploadSpeed,download_speed=$downloadSpeed";
				$sql .= ",connect_time=0,complete_time=$timenow $snatchWhere AND complete_time=0";
				execute($sql);
				$debug['sql'][] = $sql;
			}
			else
			{
				//已有完成过的记录，将旧记录拿到即将完成的记录上并删除旧记录
				$completeSnatch = $completeSnatch[0];
				$sql = "DELETE FROM snatch WHERE id=".$completeSnatch['id'];
				execute($sql);//删除旧记录
				$debug['sql'][] = $sql;
				$sql = "UPDATE snatch SET peer_id='{$_GET['peer_id']}',ip='$ip',port={$_GET['port']},uploaded=uploaded+$uploadThis+{$completeSnatch['uploaded']},downloaded=downloaded+$downloadThis+{$completeSnatch['downloaded']},`left`={$_GET['left']},is_seeder=$isSeeder,last_report_time=this_report_time,this_report_time=$timenow,connectable=$connectable,agent='$agent',upload_speed=$uploadSpeed,download_speed=$downloadSpeed,connect_time={$completeSnatch['connect_time']},complete_time={$completeSnatch['complete_time']}";
				$sql .= " $snatchWhere AND complete_time=0";
				execute($sql);
				$debug['sql'][] = $sql;
			}
		}
		else//未完成，这次过后还没完成
		{
			$sql = "UPDATE snatch SET peer_id='{$_GET['peer_id']}',ip='$ip',port={$_GET['port']},uploaded=uploaded+$uploadThis,downloaded=downloaded+$downloadThis,`left`={$_GET['left']},is_seeder=$isSeeder,last_report_time=this_report_time,this_report_time=$timenow,connectable=$connectable,agent='$agent',upload_speed=$uploadSpeed,download_speed=$downloadSpeed,connect_time=connect_time+$duration";
			$sql .= " $snatchWhere AND complete_time = 0";
			execute($sql);
			$debug['sql'][] = $sql;
		}
	}
}
elseif ($isSeeder && !isset($isCompleted))//完成下载后的交互
{
	//先看有没有，做种者第一次交互是没有数据的。发种者complete_time为0，以此区分取完成情况
	$checkSeederSnatchSql = "SELECT id FROM snatch $snatchWhere AND is_seeder=1 LIMIT 1";
	$seederSnatch = query($checkSeederSnatchSql);
	if (empty($seederSnatch))
	{
		$sql = 'INSERT INTO snatch (torrent_id, torrent_size, peer_id, ip, port, uploaded, downloaded, `left`, is_seeder, start_time, last_report_time, this_report_time, user_id, connectable, agent, passkey, upload_speed, download_speed, connect_time, complete_time) VALUES (';
		$sql .= "{$torrent['id']}, {$torrent['size']}, '{$_GET['peer_id']}', '$ip', {$_GET['port']}, $uploadThis, $downloadThis, {$_GET['left']}, $isSeeder, $timenow, $timenow, $timenow, {$userInfo['id']}, $connectable, '$agent', '{$_GET['passkey']}', $uploadSpeed, $downloadSpeed, $duration, 0)";
	}
	else
	{
		$sql = "UPDATE snatch SET peer_id='{$_GET['peer_id']}',ip='$ip',port={$_GET['port']},uploaded=uploaded+$uploadThis,last_report_time=this_report_time,this_report_time=$timenow,connectable=$connectable,agent='$agent',connect_time=connect_time+$duration WHERE id=".$seederSnatch[0]['id'];
	}
	execute($sql);
	$debug['sql'][] = $sql;
}


$fopen = fopen('debug_log', 'a');
fwrite($fopen, serialize($debug)."\r\n");
fclose($fopen);
unset($fopen);

$fopen = fopen('sql_log', 'a');
fwrite($fopen, '**************************END****'.microtime(true).'----'.(microtime(true)-START).'*******************************'."\r\n");
fclose($fopen);
unset($fopen);
//the last step，返回peer信息！
error($returnDict, TRUE);






