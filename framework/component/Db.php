<?php
namespace framework\component;

use framework\App as App;
use framework\core\Log as Log;

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
	private $lastSql;
	private $active = FALSE;
	
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
		$this->lastSql = $sql;
		if(self::$_debug)
		{
			$optionStr = $sql;
			if(!empty($options))
			{
				foreach($options as $key=>$value)
				{
					$optionStr .= '，'.$key.'=>'.$value;
				}
			}
			
			Log::executeSql($optionStr);
		}
		self::$_PDOStat = self::$_link->prepare($sql);
		try
		{
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
		$this->lastSql = $sql;
		if(self::$_debug)
		{
			Log::executeSql($sql);
		}
		try
		{
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
	public function getAllBySql($sql = '', $options = array(), $fetchStyle = \PDO::FETCH_ASSOC)
	{
		if(empty($sql) || !is_array($options))
		{
			trigger_error('参数错误：sql语句不能为空且绑定参数须以数组形式传递', E_USER_ERROR);
			return '';
		}
		$this->_query($sql, $options);
		return self::$_PDOStat->fetchAll($fetchStyle);	
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
		$this->_query($sql, $options);
		return self::$_PDOStat->fetch($fetchStyle);
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
		if(stripos(trim($sql), 'INSERT') === 0)
		{
			//插入操作
			$this->_exec($sql);
			return self::$_link->lastInsertId();
		}
		else 
		{
			$this->_query($sql, $options);
			return self::$_PDOStat->rowCount();
		}
		
	}
	
	public function count($sql = '', $options = array())
	{
		if(empty($sql) || !is_array($options))
		{
			return FALSE;
		}
		$this->_query($sql, $options);
		return self::$_PDOStat->fetchColumn();
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
	
	

}