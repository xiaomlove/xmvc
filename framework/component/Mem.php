<?php
namespace framework\component;

use framework\App;

final class Mem extends \Memcache
{
	private static $_instance = NULL;
	
	private function __construct()
	{
		$config = App::getConfig(array('component', 'Memcache'));
// 		var_dump($config);exit;
		if (empty($config) || empty($config['host']) || empty($config['port']))
		{
			trigger_error('没有配置Memcache', E_USER_ERROR);
		}
		$connect = $this->connect($config['host'], $config['port']);
		if (!$connect)
		{
			trigger_error('Memcache连接失败', E_USER_ERROR);
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
	
	public function get($key, $flag = NULL)
	{
		if (defined('NO_CACHE') && (NO_CACHE === TRUE || NO_CACHE === 1))
		{
			return FALSE;
		}
		return parent::get($key, $flag);
	}
	
	public function set($key, $var, $flag = 0, $expire = 1800)
	{
		if (defined('NO_CACHE') && (NO_CACHE === TRUE || NO_CACHE === 1))
		{
			return TRUE;
		}
		return parent::set($key, $var, $flag, $expire);
	}
	
}