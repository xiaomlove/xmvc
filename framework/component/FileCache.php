<?php
namespace framework\component;

use framework\App;
use framework\core\CacheInterface;
use framework\helper\StringHelper;

final class FileCache implements CacheInterface
{
	private static $_instance = NULL;
	
	private static $path;
	
	private static $expire = 1800;//默认缓存时间，30min
	
	private static $folderRule = 'Ymd';//默认创建目录规则，使用Date()函数
	
	private static $folderPrefix = '';//默认目录前缀
	
	private static $folderSuffix = '';//默认目录后缀
	
	private function __construct()
	{
		$config = App::getConfig(array('component', 'FileCache'));
		if (empty($config) || empty($config['path']))
		{
			trigger_error('没有配置FileCache', E_USER_ERROR);
		}
		if (!empty($config['folderRule']))
		{
			self::$folderRule = $config['folderRule'];
		}
		if (!empty($config['folderPrefix']))
		{
			self::$folderPrefix = $config['folderPrefix'];
		}
		if (!empty($config['folderSuffix']))
		{
			self::$folderSuffix = $config['folderSuffix'];
		}
		
		$path = App::getPathOfAlias($config['path']);
		$path .= self::$folderPrefix.date(self::$folderRule).self::$folderSuffix.DS;
		if (!is_dir($path))
		{
			$create = mkdir($path, 0777, TRUE);
			if ($create === FALSE)
			{
				trigger_error('无法创建FileCache目录：'.$path, E_USER_ERROR);
			}
		}
		self::$path = $path;
		if (!empty($config['expire']))
		{
			self::$expire = $config['expire'];
		}
	}
	
	public function __set($prop, $value)
	{
		return FALSE;
	}
	
	public function __get($prop)
	{
		$props = get_class_vars(__CLASS__);
		unset($props['_instance']);
		if (in_array($prop, $props))
		{
			return self::$$prop;
		}
	}
	
	private function __clone()
	{
	
	}
	
	public static function getInstance()
	{
		if (self::$_instance === NULL)
		{
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	public function set($key, $var, $expire = '')
	{
		$file = self::$path.md5($key);
		if (empty($expire))
		{
			$expire = self::$expire;
		}
		$data = serialize(array('data' => $var, 'tinyhd_file_cache_expire' => $expire));
		return file_put_contents($file, $data);
	}
	
	public function get($key)
	{
		$file = self::$path.md5($key);
		$result = file_get_contents($file);
		if ($result === FALSE)
		{
			return FALSE;
		}
		$filemtime = filemtime($file);//上次修改时间
		$data = unserialize($result);
		$expire = $data['tinyhd_file_cache_expire'];
		if ($filemtime + $expire > TIME_NOW)
		{
			return FALSE;//已过期
		}
		else
		{
			return $data['data'];
		}
	}
	
	public function delete($key)
	{
		$file = self::$path.md5($key);
		$encodeFileName = StringHelper::encodeFileName($file);//中文+Win系统有问题，得编码一下
		if (file_exists($encodeFileName))
		{
			return unlink($encodeFileName);
		}
		else 
		{
			return TRUE;
		}
	}
	
	public function clear()
	{
		$path = self::$path;
		$result = self::_doScanDir($path);
		foreach ($result['files'] as $file)
		{
			unlink($file);//中文+Win的问题不考虑了，一般缓存目录+缓存文件名不含中文
		}
		foreach ($result['dirs'] as $dir)
		{
			rmdir($dir);
		}
		return TRUE;
	}
	
	public function increase($key, $value = 1)
	{
		//文件缓存这个没啥意义,太慢了，估计都不如Mysql。只有内存缓存才有意义
		trigger_error('FileCache中'.__FUNCTION__.'无意义', E_USER_NOTICE);
		return FALSE;
	}
	
	public function decrease($key, $value = 1)
	{
		//同increase
		trigger_error('FileCache中'.__FUNCTION__.'无意义', E_USER_NOTICE);
		return FALSE;
	}
	
	public function getKeys()
	{
		$path = self::$path;
		$result = self::_doScanDir($path);
		return $result['files'];//只需文件
	}
	
	/**
	 * 扫描目录，获得所有文件及目录
	 * @param unknown $dir
	 * @return Ambigous <unknown, string, multitype:multitype: >
	 */
	private static function _doScanDir($dir)
	{
		$result = array('files' => array(), 'dirs' => array());
		$fileList = scandir($dir);
		foreach ($fileList as $file)
		{
			if ($file === '.' || $file === '..')
			{
				continue;
			}
			$tmpFile = $dir.$file;
			if (is_file($tmpFile))
			{
				$result['files'][] = $tmpFile;
			}
			elseif (is_dir($tmpFile.DS))
			{
				$result['dirs'][] = $tmpFile;
				$tmpFileList = self::_doScanDir($tmpFile.DS);
				$result['files'] = array_merge($result['files'], $tmpFileList['files']);
				$result['dirs'] = array_merge($result['dirs'], $tmpFileList['dirs']);
			}
		}
		$result['dirs'] = array_reverse($result['dirs']);//目录反转，由深到浅排列便于删除
		return $result;
	}
}