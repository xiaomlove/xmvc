<?php
namespace framework\component;

use framework\App;
use framework\core\CacheInterface;

final class Mem extends \Memcache implements CacheInterface
{
	private static $_instance = NULL;
	
	private static $host;
	
	private static $port;
	
	private function __construct()
	{
		$config = App::getConfig(array('component', 'Memcache'));
// 		var_dump($config);exit;
		if (empty($config) || empty($config['host']) || empty($config['port']))
		{
			trigger_error('没有配置Memcache', E_USER_ERROR);
		}
		self::$host = $config['host'];
		self::$port = $config['port'];
		
		$connect = $this->connect($config['host'], $config['port']);
		if (!$connect)
		{
			trigger_error("无法连接Memcache,host：".self::$host."，port：".self::$port, E_USER_ERROR);
		}
	}
	
	private function __clone()
	{
		
	}
	
// 	public function __set($prop, $value)
// 	{
// 		return FALSE;//这个不能重写，否则报Warning: No servers added to memcache connection 
// 	}
	
	public function __get($prop)
	{
		if ($prop === 'host' || $prop === 'port')
		{
			return self::$$prop;
		}
	}
	
	public static function getInstance()
	{
		if (self::$_instance === NULL)
		{
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	public function get($key)
	{
		if (defined('NO_CACHE') && (NO_CACHE === TRUE || NO_CACHE === 1))
		{
			return FALSE;
		}
		return parent::get($key);
	}
	
	public function set($key, $var, $expire = 1800)
	{
		if (defined('NO_CACHE') && (NO_CACHE === TRUE || NO_CACHE === 1))
		{
			return TRUE;
		}
		return parent::set($key, $var, 0, $expire);
	}
	
	public function delete($key)
	{
		return parent::delete($key);
	}
	
	public function clear()
	{
		return parent::flush();
	}
	
	public function increase($key, $value = 1)
	{
		return parent::increment($key, $value);
	}
	
	public function decrease($key, $value = 1)
	{
		return parent::decrement($key, $value);
	}
	
	public function getKeys()
	{
		$host = self::$host;
		$port = self::$port;
		
		$items = self::$_instance->getExtendedStats('items');
		$items = $items["$host:$port"]['items'];
		$out = array();
		foreach($items as $key => $values)
		{
			$number = $key;;
			$str = self::$_instance->getExtendedStats("cachedump", $number, 0);
			$line = $str["$host:$port"];
			if( is_array($line) && count($line) > 0)
			{
				foreach($line as $k => $v)
				{
					$result = self::$_instance->get($k);
					if ($result !== FALSE)
					{
						$out[] = $k;
					}
				}
			}
		}
		return $out;
	}
	
}