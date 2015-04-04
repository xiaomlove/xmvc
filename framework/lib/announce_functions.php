<?php
require 'framework/lib/BEncode.php';

/**
 * 返回信息函数
 * Enter description here ...
 * @param unknown_type $msg 要返回的内容
 * @param unknown_type $notError 是否非错误信息
 */
function error($msg, $notError = FALSE)
{
	if (!$notError)
	{
		$out = BEncode::encode(array(
				'isDict' => TRUE,
				'failure reason' => $msg,
		));
	}
	else
	{
		$out = $msg;//用于最后输出正确的返回信息
	}
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

/**
 * 检测是否浏览器访问，禁止之
 * Enter description here ...
 */
function denyBrowser()
{
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
	return $agent;
}

/**
 * 创建pdo对象，连接数据库
 * Enter description here ...
 */
function connectDB($config)
{
	//$config = require 'application/protected/config/config.php';
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
	return $pdo;
}
/**
 * 用于select操作，返回所有记录二维索引数组
 * @param unknown $sql
 * @param unknown $options
 * @return multitype:
 */
function query($sql, $options = array())
{
	$fopen = fopen('sql_log', 'a');
	$time = date('Y-m-d H:i:s');
	fwrite($fopen, $time.'--'.$sql."\r\n");
	if (!empty($options))
	{
		fwrite($fopen, '--options:'.serialize($options)."\r\n");
	}
	fwrite($fopen, '----------------------------------------------------'."\r\n");
	fclose($fopen);
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
	$fopen = fopen('sql_log', 'a');
	$time = date('Y-m-d H:i:s');
	fwrite($fopen, $time.'--'.$sql."\r\n");
//	fwrite($fopen, '--options:'.serialize($options)."\r\n");
	fwrite($fopen, '----------------------------------------------------'."\r\n");
	fclose($fopen);
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
