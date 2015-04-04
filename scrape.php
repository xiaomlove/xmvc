<?php
define('DEBUG', TRUE);
//define('MODE', 'RELEASE');


if (defined('DEBUG') && DEBUG)
{
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 'Off');
	ini_set('log_errors', 'On');
	ini_set('error_log', 'scrape_error_log');
}
require 'framework/lib/announce_functions.php';
$agent = denyBrowser();

//请求scrape只会发送passkey和info_hash
if (!isset($_GET['passkey']) || strlen($_GET['passkey']) !== 32)
{
	error('error passkey');
}
if (isset($_GET['info_hash']) && strlen($_GET['info_hash']) !== 20)
{
	error('error info_hash');//这个参数有可能没有传递？
}
//没有info_hash时，返回所有种子数的peer？有则返回该info_hash对应的种子的peer??参考自nexusphp

$fields = 'info_hash, finish_times, seeder_count, leecher_count';
$sql = "SELECT $fields FROM torrent ";
if (isset($_GET['info_hash']))
{
	$unpack = unpack('H*', $_GET['info_hash']);
	$info_hash = $unpack[1];
	$sql .= "WHERE info_hash=:info_hash";
	$option = array(':info_hash' => $info_hash);
}
else
{
	$sql .= "WHERE 1";
	$option = array();
}
$pdo = connectDB();
$result = query($sql, $option);
if (empty($result))
{
	error('torrent not exists');
}
$out = array('isDict' => TRUE);
$out['files'] = array('isDict' => TRUE);//这里面结构有点不明朗，不知道对不对
foreach ($result as $torrent)
{
	$out['files'][] = array(
		'isDict' => TRUE,
		$torrent['info_hash'] => array(
			'isDict' => TRUE,
			'complete' => $torrent['seeder_count'],
			'downloaded' => $torrent['finish_times'],
			'incomplete' => $torrent['leecher_count']
		)
	);
}
$returnDict = BEncode::encode($out);
if (empty($returnDict))
{
	trigger_error('scrape encode error', E_USER_ERROR);
	exit();
}
error($returnDict, TRUE);





