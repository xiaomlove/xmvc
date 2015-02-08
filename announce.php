<?php
/**
 * 为了速度，面向过程一步一步的来。
 */

//-1，如果测试模式，输出错误
define('DEBUG', TRUE);
if (defined('DEBUG') && DEBUG)
{
	error_reporting(E_ALL | E_STRICT);//所有错误都报告
	ini_set('display_errors', 'Off');
	ini_set('log_errors', 'On');
	ini_set('error_log', 'announce_error_log');
}
//0、引入必须的BEncode类，定义返回错误信息的函数
define('TIMENOW', $_SERVER['REQUEST_TIME']);
require 'framework/lib/BEncode.php';
function error($msg)
{
	$out = BEncode::encode(array(
		'isDict' => TRUE,
		'failure reason' => $msg,
	));
	header('Content-Type: text/plain; charset = utf-8');
	header("Pragma: no-cache");
	if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && $_SERVER['HTTP_ACCEPT_ENCODING'] === 'gzip' && function_exists('gzencode'))
	{
		header("Content-Encoding: gzip");
		echo gzencode($out, 9, FORCE_GZIP);
	}
	else 
	{
		echo $out;
	}
	exit();
}

//1、检查参数是否齐全以及合法，$_GET传递过来的都是字符串类型
if (!isset($_GET['passkey']) || strlen($_GET['passkey']) !== 32)
{
	error('error passkey');
}
if (!isset($_GET['info_hash']) || strlen(urldecode($_GET['info_hash'])) !== 20)//是原始sha1值20字节，传送时经过了urlencode就不定了
{
	error('error info_hash');
}
if (!isset($_GET['peer_id']) || strlen(urldecode($_GET['peer_id'])) !== 20)//同上，需要urldecode回去判断
{
	error('error peer_id');
}
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
	$eventAllow = array('started', 'stoped', 'completed');
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
if(!isset($_SERVER['HTTP_USER_AGENT']))
{
	error('no agent');
}
if(!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET')
{
	error('not get request');
}
$agent = $_SERVER['HTTP_USER_AGENT'];
$browserKeyWords = array('MSIE', 'Trident', 'Chrome', 'Firefox', 'Chromium', 'Android', 'Safari', 'Opera', 'microsoft internet explorer');
foreach ($browserKeyWords as $key)
{
	if (stripos($agent, $key) !== FALSE)
	{
		error('browser detect');
	}
}
unset($key);

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
$config = require 'application/protected/config/config.php';
$dbConfig = $config['database'];
try
{
	$pdo = new PDO($dbConfig['connectionString'], $dbConfig['username'], $dbConfig['password']);
}
catch (PDOException $e)
{
	trigger_error('数据库连接错误：'.$e->getMessage());
}
$pdo->exec('SET NAMES '.$dbConfig['charset']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
/**
 * 用于select操作，返回所有记录二维索引数组
 * @param unknown $sql
 * @param unknown $options
 * @return multitype:
 */
function query($sql, $options = array())
{
	global $pdo;
	$stat = $pdo->prepare($sql);
	if ($stat === FALSE)
	{
		trigger_error('pdo prepare error:'.$sql, E_USER_ERROR);
		exit();
	}
	$result = $stat->execute($options);
	if ($result)
	{
		return $stat->fetchAll(PDO::FETCH_ASSOC);
	}
	else
	{
		trigger_error('pdo execute error:'.$sql, E_USER_ERROR);
		exit();
	}
}
/**
 * 执行insert操作，返回最后记录的id；执行update/delete操作，返回受影响的记录数
 * @param unknown $sql
 * @return string|number
 */
function execute($sql)
{
	global $pdo;
	$result = $pdo->exec($sql);
	if ($result !== FALSE)
	{
		if (stripos($sql, 'INSERT') !== FALSE)
		{
			//插入操作
			return $pdo->lastInsertId();
		}
		else
		{
			return $result;
		}
	}
	else
	{
		trigger_error('pdo exec error:'.$sql, E_USER_ERROR);
		exit();
	}
	
}

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
$unpack = unpack('H*', urldecode($_GET['info_hash']));//转化为数据库存的40位的16进制版本
$info_hash = $unpack[1];
$sql = 'SELECT id, add_time, seeder_count, leecher_count, user_id, is_deleted FROM torrent WHERE info_hash=:info_hash LIMIT 1';
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
if (intval($_GET['left']) === 0 )
{
	$isSeeder = TRUE;//标记为做种者
}
else 
{
	$isSeeder = FALSE;
}

$torrentPeerNum = intval($torrent['seeder_count']) + intval($torrent['leecher_count']);//种子中记录的peers数量
$sql = 'SELECT is_seeder, peer_id, ip, port, downloaded, uploaded, left, start_time, last_report_time, connect_time FROM peer WHERE torrent_id='.$torrent['id'];
$peerSelf = query($sql.' AND user_id=:user_id LIMIT 1', array(':user_id' => $userInfo['id']));
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
	if (TIMENOW - $peerSelf['last_report_time'] < 30)
	{
		error('min interval 30s');
	}
}

$peerList = query($sql.' AND user_id<>'.$userInfo['id']);//所有该种子的peer列表，不包括自己
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

$interval = 1800;//默认常规请求间隔，30分钟
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
				'port' => $peer['port']
			);
		}
		else 
		{
			$return['peers'][] = array(
				'isDict' => TRUE,
				'peer id' => $peer['peer_id'],
				'ip' => $peer['ip'],
				'port' => $peer['port']
			);
		}
	}
}

//10、应该算拼凑完毕，接下来是更新相关数据了吧






