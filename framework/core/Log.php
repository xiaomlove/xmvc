<?php
namespace framework\core;

use framework\App;

final class Log
{
	private static $_startTime = NULL;
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
	
	public static function start()
	{
		if(self::$_startTime === NULL)
		{
			self::$_startTime = microtime(true);
		}		
	}
	public static function errorCatch($level, $msg, $file, $line)
	{
		self::$_errors[] = array('level'=>self::$_error_constant[$level], 'msg'=>$msg, 'file'=>$file, 'line'=>$line, 'backTrace'=>debug_backtrace());
// 		die();不应该手动停止，由程序错误到一定程度自动停止
	}
	
	public static function requireFile($file)
	{
		self::$_requireFiles[] = array('time'=>microtime(true), 'memory'=>memory_get_usage(true), 'fileName'=>$file);	
	}
	
	public static function executeSql($sql)
	{
		self::$_sqls[] = $sql;
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
		echo '<div id="wrap" class="container" style="margin-top: 100px"><h3 class="page-header">引入文件：</h3>';
		$files = self::getRequireFiles();
		if(count($files))
		{
			echo '<ol id="require-file">';
			foreach($files as $file)
			{
				echo '<li>时间：'.$file['time'].'，内存：'.($file['memory']/8/1024).'KB，位置：'.$file['fileName']. '</li>';
			}
			echo '</ol>';
		}
		
		
		echo '<h3 class="page-header">sql语句：</h3>';
		$sqls = self::getSqls();
		if(count($sqls))
		{
			echo '<ol id="sql">';
			foreach($sqls as $sql)
			{
				echo '<li>'.$sql.'</li>';
			}
			echo '</ol>';
		}
		
		
		echo '<h3 class="page-header">错误信息：</h3>';
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
		
		$endTime = microtime(true);
		echo '<h1>结束时间：'.$endTime.'，脚本总耗时：'.number_format(($endTime-self::$_startTime), 4).'秒</h1></div>';

	}
}