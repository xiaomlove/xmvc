<?php
namespace framework\core;

use framework\App;

final class Log
{
	private static $_requireFiles = array();
	private static $_sqls = array();
	private static $_errors = array();
	private static $_error_constant = array(
// 						E_COMPILE_ERROR=>'E_COMPILE_ERROR',//这些不能被set_error_handler捕获，一般也不会发生
// 						E_COMPILE_WARNING=>'E_COMPILE_WARNING',
// 						E_CORE_ERROR=>'E_CORE_ERROR',
// 						E_CORE_WARNING=>'E_CORE_WARNING',
						E_DEPRECATED=>'E_DEPRECATED',
// 						E_ERROR=>'E_ERROR',
						E_NOTICE=>'E_NOTICE',
// 						E_PARSE=>'E_PARSE',
						E_RECOVERABLE_ERROR=>'E_RECOVERABLE_ERROR',
						E_STRICT=>'E_STRICT',
						E_USER_DEPRECATED=>'E_USER_DEPRECATED',
						E_USER_ERROR=>'E_USER_ERROR',
						E_USER_NOTICE=>'E_USER_NOTICE',
						E_USER_WARNING=>'E_USER_WARNING',
						E_WARNING=>'E_WARNING',
						1=>'FATAL_ERROR',
					);
	
	public static function errorCatch($level, $msg, $file, $line)
	{
		self::$_errors[] = array('level'=>self::$_error_constant[$level], 'msg'=>$msg, 'file'=>$file, 'line'=>$line, 'backTrace'=>debug_backtrace());
// 		die();不应该手动停止，由程序错误到一定程度自动停止
	}
	
	public static function requireFile($file)
	{
		self::$_requireFiles[] = array('time'=>microtime(true), 'memory'=>memory_get_usage(true), 'fileName'=>$file);	
	}
	
	public static function executeSql($sqlInfo)
	{
		self::$_sqls[] = $sqlInfo;
	}
	
	private static function getRequireFiles()
	{
		return self::$_requireFiles;
	}
	
	private static function getSqls()
	{
		return self::$_sqls;
	}
	
	private static function getErrors()
	{
		return self::$_errors;
	}
	
	public static function outPutLog()
	{
		if((App::ins()->request->isAjax() && defined('NO_LOG_AJAX') && NO_LOG_AJAX) || App::getConfig('noLog'))
		{
			return;
		}
		$lastError = error_get_last();
		if($lastError['type'] === 1)
		{
			self::$_errors[] = array('level'=>self::$_error_constant[1], 'msg'=>$lastError['message'], 'file'=>$lastError['file'], 'line'=>$lastError['line']);
		}
		echo '<div id="wrap" class="container" style="margin-top: 100px"><h1 class="">引入文件：</h1>';
		$files = self::getRequireFiles();
		if(count($files))
		{
			echo '<table class="table table-condensed table-hover"><thead><tr><th>序号</th><th>时间</th><th>内存占用(KB)</th><th>文件路径</th></tr></thead><tbody>';
			foreach($files as $num => $file)
			{
				echo '<tr><td>'.($num + 1).'</td><td>'.$file['time'].'</td><td>'.($file['memory']/8/1024).'</td><td>'.$file['fileName']. '</td></tr>';
			}
			echo '</tbody></table>';
		}
		
		
		echo '<h1 class="">SQL语句：</h1>';
		$sqls = self::getSqls();
		if(count($sqls))
		{
			echo '<table class="table table-condensed table-hover">';
			echo '<thead><tr><th style="min-width: 50px">序号</th><th>执行时间</th><th>语句</th><th style="min-width: 75px">绑定数据</th></tr></thead>';
			echo '<tbody>';
			$totalExecTime = 0;
			foreach($sqls as $k => $sql)
			{
				$totalExecTime += $sql['time'];
				echo '<tr><td>'.($k+1).'</td><td>'.number_format($sql['time'], 6).'</td><td>'.$sql['sql'].'</td><td>'.self::_arrToStr($sql['bind']).'</td></tr>';
			}
			echo '</tbody>';
			echo '<tfoot><tr><td colspan="4"><strong>总耗时：'.$totalExecTime.'(秒)</strong></td></tr></tfoot></table>';
		}
		
		
		echo '<h1 class="page-header">错误信息：</h1>';
		$errors = self::getErrors();
		if(count($errors))
		{
			foreach($errors as $error)
			{
				echo '<div class="error"><h4>'.$error['level'], '&nbsp;&nbsp;', $error['msg'], '&nbsp;&nbsp;发生在文件', $error['file'], '&nbsp;&nbsp;的第', $error['line'], '行</h4>';
				if(count($error['backTrace']))
				{
					
					foreach($error['backTrace'] as $key=>$backTrace)
					{
						$out = '#'.$key.' ';
						if(isset($backTrace['class']))
						{
							$out .= $backTrace['class'].$backTrace['type'];
						}
						if(isset($backTrace['function']))
						{
							$out .= $backTrace['function'].'()';
						}
						$out .= ' call at ['.$backTrace['file'].':'.$backTrace['line'].']';
						echo $out.'<br/>';
					}
				}
			}	
		}
		if (App::isComponentEnabled('Memcache'))
		{
			echo '<h1>Memcache缓存中的有效键：</h1>';
// 			self::_outPutMemcache();
			$memKeys = App::ins()->mem->getKeys();
			if (!empty($memKeys))
			{
				foreach ($memKeys as $key)
				{
					echo $key.'<br/>';
				}
			}
		}
		$endTime = microtime(true);
		echo '<h1>结束时间：'.$endTime.'，脚本总耗时：'.number_format(($endTime - App::getStartTime()), 4).'秒</h1></div>';

	}
	private static function _arrToStr($arr)
	{
		$out = '';
		foreach ($arr as $key => $value)
		{
			$out .= $key.'=>'.$value;
		}
		return $out;
	}
	
	private static function _outPutMemcache()
	{
		$memConfig = App::getConfig(array('component', 'Memcache'));
		$host = $memConfig['host'];
		$port = $memConfig['port'];
		$mem = App::ins()->mem;
		$items=$mem->getExtendedStats ('items');
		$items=$items["$host:$port"]['items'];
		if (count($items) === 0)
		{
			return;
		}
		foreach($items as $key=>$values)
		{
			$number=$key;;
			$str=$mem->getExtendedStats ("cachedump",$number,0);
			$line=$str["$host:$port"];
			if( is_array($line) && count($line)>0){
				foreach($line as $k=>$v){
					$result = $mem->get($k);
					if ($result  !== FALSE)
					{
						echo $k.'<br/>';
					}
					// print_r($mem->get($key));
					// echo "<p>";
				}
			}
		}
	}
}