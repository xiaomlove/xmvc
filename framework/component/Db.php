<?php
namespace framework\component;

use framework\App;
use framework\core\Log;

class Db
{
	private static $_link = NULL;
	private static $_instance = NULL;
	private static $_config = NULL;
	private static $_PDOStat = NULL;
	private static $_debug = FALSE;
	private $prefix;
	private $serverVersion;
	private $serverInfo;
	private $clientVersion;
	private $lastSql = NULL;
	private $active = FALSE;
	private $queries = 0;
	
	//'SELECT%DISTINCT% %FIELD% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%COMMENT%'
	private function __construct()
	{
		if(!class_exists('PDO'))
		{
			trigger_error('不支持PDO，请先开启！', E_USER_ERROR);
			exit();
		}
		if(defined('DEBUG') && (DEBUG === 1 || DEBUG === true))
		{
			self::$_debug = TRUE;
		}
		$config = App::getConfig('database');
		$this->prefix = isset($config['tablePrefix']) ? $config['tablePrefix'] : '';
// 		var_dump($config);exit;
		if(empty($config) || empty($config['connectionString']) || empty($config['username']) || empty($config['password']))
		{
			trigger_error('数据库配置错误！', E_USER_ERROR);
			exit();
		}
		if(empty($config['charset']))
		{
			$config['charset'] = 'utf8';
		}
		self::$_config = $config;
		try
		{
			$pdo = self::$_link = new \PDO($config['connectionString'], $config['username'], $config['password']);
		}
		catch(\PDOException $e)
		{
			trigger_error('数据库连接错误：'.$e->getMessage());
		}
		$pdo->exec('SET NAMES '.$config['charset']);
		$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);//设置错误处理模式，抛异常
		$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);//禁用防真效果，有效防sql注入
// 		$pdo->setAttribute(\PDO::ATTR_ORACLE_NULLS, true);//设置数据库中的NULL对应php中的NULL，还有不变或空字符串。
		$this->serverVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
		$this->serverInfo = $pdo->getAttribute(\PDO::ATTR_SERVER_INFO);
		$this->clientVersion = $pdo->getAttribute(\PDO::ATTR_CLIENT_VERSION);
		unset($config, $pdo);
		
	}
	
	public function __set($name, $value)
	{
		if($name === 'active')
		{
			$this->active = $value;
		}
	}
	
	public function __get($name)
	{
		$getable = array('prefix', 'serverVersion', 'serverInfo', 'clientVersion', 'lastSql');
		if(in_array($name, $getable))
		{
			return $this->$name;
		}
	}
	
	private function __clone()
	{
		
	}
	
	public static function getInstance()
	{
		if(self::$_instance instanceof self)
		{
			return self::$_instance;
		}
		self::$_instance = new self;
		return self::$_instance;
	}
	
	/**
	 * 添加这种块注释的方法：键入/**，再按回车
	 * 预处理一条sql语句,获得PDOStatment对象，并执行预处理语句。
	 * @param string $sql sql语句
	 * @param array $options 要绑定的数据，通过占位符
	 * 
	 */
	private function _query($sql, $options)
	{
		if(!empty(self::$_PDOStat))
		{
			self::_freePDOStat();
		}
		$this->lastSql = $sql.(!empty($options) ? http_build_query($options) : '');
		self::$_PDOStat = self::$_link->prepare($sql);
		try
		{
			$this->queries += 1;
			self::$_PDOStat->execute($options);
		}
		catch(\PDOException $e)
		{
			trigger_error($e->getMessage());
			exit();
		}
	}
	
	/**
	 * 执行exec()
	 * @param unknown $sql
	 */
	private function _exec($sql)
	{
		try
		{
			$this->queries += 1;
			return self::$_link->exec($sql);
		}
		catch(\PDOException $e)
		{
			trigger_error($e->getMessage());
			exit();
		}
	}
	
	private static function _freePDOStat()
	{
		self::$_PDOStat = NULL;
	}
	
	/**
	 * 通过sql语句获取所有数据
	 * @param unknown $sql
	 * @param unknown $fetchStyle
	 * @return multitype:
	 */
	public function getAllBySql($sql = '', $options = array(), $cacheExpire = '', $fetchStyle = \PDO::FETCH_ASSOC)
	{
		if(empty($sql) || !is_array($options))
		{
			trigger_error('参数错误：sql语句不能为空且绑定参数须以数组形式传递', E_USER_ERROR);
			return '';
		}
		$doCache = FALSE;
		if (self::$_debug)
		{
			$start = microtime(TRUE);
			if ($cacheExpire !== '' && App::isComponentEnabled('Memcache'))
			{
				$cacheKey = $sql.json_encode($options);
				$result = App::ins()->mem->get($cacheKey);
				if ($result !== FALSE)
				{
					$end = microtime(TRUE);
					self::_logSql($end - $start, $sql.'【FROM CACHE】', $options);
					return $result;
				}
				else 
				{
					$doCache = TRUE;
				}
			}
			$this->_query($sql, $options);
			$result = self::$_PDOStat->fetchAll($fetchStyle);
			$end = microtime(TRUE);
			self::_logSql($end - $start, $sql, $options);
		}
		else
		{
			if ($cacheExpire !== '' && App::isComponentEnabled('Memcache'))
			{
				$cacheKey = $sql.json_encode($options);
				$result = App::ins()->mem->get($cacheKey);
				if ($result !== FALSE)
				{
					return $result;
				}
				else
				{
					$doCache = TRUE;
				}
			}
			$this->_query($sql, $options);
			$result = self::$_PDOStat->fetchAll($fetchStyle);
		}
		if ($doCache)
		{
			$cache = App::ins()->mem->set($cacheKey, $result, FALSE, $cacheExpire);
			if ($cache === FALSE)
			{
				trigger_error('Memcache set failed', E_USER_NOTICE);
			}
		}
		return $result;
	}
	
	/**
	 * 通过sql语句获取第一条数据
	 * @param unknown $sql
	 * @param unknown $fetchStyle
	 * @return multitype:
	 */
	public function getOneBySql($sql = '', $options = array(), $fetchStyle = \PDO::FETCH_ASSOC)
	{
		if(empty($sql) || !is_array($options))
		{
			trigger_error('参数错误：sql语句不能为空且绑定参数须以数组形式传递', E_USER_ERROR);
			return '';
		}
		if (self::$_debug)
		{
			$start = microtime(TRUE);
			$this->_query($sql, $options);
			$result = self::$_PDOStat->fetch($fetchStyle);
			$end = microtime(TRUE);
			self::_logSql($end - $start, $sql, $options);
		}
		else
		{
			$this->_query($sql, $options);
			$result = self::$_PDOStat->fetch($fetchStyle);
		}
		return $result;
	}
	/**
	 * 执行update,delete,insert语句，update/delete返回受影响记录条数，insert返回最后插入记录的id
	 * @param string $sql
	 * @param unknown $options
	 */
	public function execute($sql = '', $options = array())
	{
		if(empty($sql) || !is_array($options))
		{
			trigger_error('参数错误：sql语句不能为空且绑定参数须以数组形式传递', E_USER_ERROR);
			return FALSE;
		}
		$this->lastSql = $sql.(!empty($options) ? http_build_query($options) : '');
		if(stripos(trim($sql), 'INSERT') === 0)
		{
			//插入操作
			if  (self::$_debug)
			{
				$start = microtime(TRUE);
				$this->_exec($sql);
				$result = self::$_link->lastInsertId();
				$end = microtime(TRUE);
				self::_logSql($end - $start, $sql, $options);
			}
			else 
			{
				$this->_exec($sql);
				$result = self::$_link->lastInsertId();
			}
		}
		else 
		{
			if (self::$_debug)
			{
				$start = microtime(TRUE);
				$this->_query($sql, $options);
				$result = self::$_PDOStat->rowCount();
				$end = microtime(TRUE);
				self::_logSql($end - $start, $sql, $options);
			}
			else 
			{
				$this->_query($sql, $options);
				$result = self::$_PDOStat->rowCount();
			}
		}
		return $result;
	}
	
	public function count($sql = '', $options = array())
	{
		if(empty($sql) || !is_array($options))
		{
			return FALSE;
		}
		if (self::$_debug)
		{
			$start = microtime(TRUE);
			$this->_query($sql, $options);
			$result = self::$_PDOStat->fetchColumn();
			$end = microtime(TRUE);
			self::_logSql($end - $start, $sql, $options);
		}
		else
		{
			$this->_query($sql, $options);
			$result = self::$_PDOStat->fetchColumn();
		}
		return $result;
	}
	/**
	 * 开启事务
	 */
	public function beginTransaction()
	{
		return self::$_link->beginTransaction();
	}
	/**
	 * 提交事务
	 */
	public function commit()
	{
		return self::$_link->commit();
	}
	/**
	 * 回滚事务
	 * @return boolean
	 */
	public function rollBack()
	{
		return self::$_link->rollBack();
	}
	
	private static function _logSql($time, $sql, array $bind = array())
	{
		Log::executeSql(array('time' => $time, 'sql' => $sql, 'bind' => $bind));
	}
	
	public function getLastSql()
	{
		return $this->lastSql;
	}
	
	public function getQueries()
	{
		return $this->queries;
	}
}