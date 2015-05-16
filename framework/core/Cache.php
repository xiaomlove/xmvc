<?php
namespace framework\core;

use framework\App;

class Cache
{
	public static $_doPageCache = FALSE;//是否需要生成页面缓存，在Controller的render中
	public static $_doQueryCache = FALSE;//是否开启查询缓存，在Model的select等查找方法中
	
	//以下三项用于重新生成页面缓存，查询缓存从config直接取
	private static $_pageCacheExpire = 3600;//默认缓存时间，单位秒
	private static $_pageCachePath;//存储目录
	private static $_pageCacheId;//页面缓存id，用于输出页面前生成缓存
	
	private static $_config = NULL;
	public static $_urlMatch = FALSE;//通过url是否匹配到规则，匹配到了不用再通过MVCP进行匹配了
	
	private static $_queryCacheList = array();//需要生成的查询缓存列表，sql为标识,存储path路径
	private static $_queryCacheExpire = 600;
	
	public static function init($config)
	{
		if(isset($config['queryCache']) && $config['queryCache'])
		{
			self::$_doQueryCache = TRUE;
		}
		self::$_config = $config;
	}
	
	public static function checkPageCacheByUrl($url)
	{
		$config = self::$_config;
		
		if(!empty($config['pageCacheOptions']['rules']) && is_array($config['pageCacheOptions']['rules']))
		{
			$rules = $config['pageCacheOptions']['rules'];
			foreach ($rules as $rule)
			{
				if(!is_array($rule))
				{
					continue;
				}
				if(isset($rule['url']) && is_string($rule['url']))
				{
					//以url为标识进行缓存
					$urlArr = explode(',', $rule['url']);
					if($rule['url'] === '*' || in_array($url, $urlArr))
					{
						self::$_urlMatch = TRUE;//标记已经匹配到，无需再进行MVCP，在Router中进行判断
						$cache = self::_getPageCacheById($url, $rule);
						if($cache !== NULL)
						{
							return $cache;//找到缓存，直接返回
						}
						break;//符合规则即停止查找
					}
				}
			}
			
			return NULL;
		}
		else
		{
			trigger_error('pageCacheOptions必须设置且是一个数组', E_USER_NOTICE);
			return NULL;
		}
	}
	
	private static function _getPageCacheById($id, $rule)
	{
		if(!empty($rule['path']) && is_dir($result = App::getPathOfAlias($rule['path'])))
		{
			$path = $result;
		}
		elseif(!empty(self::$_config['path']) && is_dir($result = App::getPathOfAlias(self::$_config['path'])))
		{
			$path = $result;
		}
		else 
		{
			$path = APP_PATH.'runtime'.DS.'page'.DS;//默认页面缓存目录
		}
		
		if(!empty($rule['expire']) && is_string($rule['expire']))
		{
			$expire = $rule['expire'];
		}
		elseif(!empty(self::$_config['expire']) && is_string(self::$_config['expire']))
		{
			$expire = self::$_config['expire'];
		}
		else 
		{
			$expire = self::$_pageCacheExpire;
		}
		
		$file = $path.md5($id);
// 		echo '<br/>getById：'.$id.'---------'.$file.'</br>';
		if(file_exists($file))
		{
			$d = time() - filemtime($file);
			if($d > $expire)
			{
				self::_setConfig($id, $path, $expire);
				return NULL;//已过期，需要重新生成
			}
			$content = file_get_contents($file);
			if(!empty($content))
			{
				return $content;
			}
			else 
			{
				self::_setConfig($id, $path, $expire);
				return NULL;//文件为空
			}
		}
		else
		{
			self::_setConfig($id, $path, $expire);
			return NULL;
		}
	}
	
	public static function checkPageCacheByMVCP()
	{
		if(!empty(self::$_config['pageCacheOptions']['rules']) && is_array(self::$_config['pageCacheOptions']['rules']))
		{
			$id = '';//缓存标识id
			foreach (self::$_config['pageCacheOptions']['rules'] as $rule)
			{
				if(!is_array($rule))
				{
					continue;
				}
				if(!isset($rule['url']))
				{
					//判断当前请求是否符合生成缓存的规则
					$rule = array_change_key_case($rule, CASE_LOWER);
					$inRule = self::_checkToCache($rule);
					if(!$inRule)
					{
						continue;
					}
					
					if(!empty($rule['params']) && is_array($rule['params']))
					{
						$diff = array_diff_assoc($rule['params'], $_GET);
						if(!empty($diff))
						{
							continue;
						}
					}
					
					//当前请求符合缓存规则，查找缓存是否存在
					$mvc = array('module'=>'0', 'controller'=>'0', 'action'=>'0');//默认
					
					$params = array();
					if(isset($rule['params']))
					{
						$params = $rule['params'];
						unset($rule['params']);
					}
// 					var_dump($rule);
// 					var_dump($mvc);
// 					var_dump($params);
					$rule = array_merge($mvc, $rule, $params);
// 					var_dump($rule);
					foreach ($rule as $key=>$value)
					{
						$id .= $key.'='.$value.'&';
					}
					$id = rtrim($id, '&');
					$cache = self::_getPageCacheById($id, $rule);
					if($cache !== NULL)
					{
						return $cache;
					}
					break;//找到了一个符合的规则，停止查找剩下的
				}
			}
			
			return NULL;
		}
		else 
		{
			trigger_error('pageCacheOptions必须设置且是一个数组', E_USER_NOTICE);
			return NULL;
		}
	}
	
	/**
	 * 检查当前MVC是否在缓存规则之列
	 * @param unknown $MVC
	 */
	private static function _checkToCache($rule)
	{
		if(!isset($rule['module']) && !isset($rule['controller']) && !isset($rule['action']))
		{
			return FALSE;
		}
		if(isset($rule['module']) && $rule['module'] != strtolower(MODULE))
		{
			return FALSE;
		}
		if(isset($rule['controller']) && $rule['controller'] != strtolower(CONTROLLER))
		{
			return FALSE;
		}
		if(isset($rule['action']) && $rule['action'] != strtolower(ACTION))
		{
			return FALSE;
		}
		return TRUE;
	}
	
	public static function setPageCache($content)
	{
		if(!is_string($content))
		{
			trigger_error('页面缓存必须是写入字符串', E_USER_ERROR);
		}
		$dir = self::$_pageCachePath;
		if(!is_dir($dir))
		{
			$create = mkdir($dir, 0777, true);
			if(!$create)
			{
				trigger_error('无法创建缓存文件夹：'.$dir, E_USER_ERROR);
			}
		}
		$fileName = $dir.md5(self::$_pageCacheId);
// 		echo '<br/>set：'.self::$_pageCacheId.'-------'.$fileName.'</br>';
		$filePut = file_put_contents($fileName, $content);
		if($filePut === FALSE)
		{
			trigger_error('生成页面缓存失败！', E_USER_NOTICE);
		}
		
	}
	
	private static function _setConfig($id, $path, $expire)
	{
		self::$_doPageCache = TRUE;
		self::$_pageCacheId = $id;
		self::$_pageCachePath = $path;
		self::$_pageCacheExpire = $expire;
	}
	
	public static function getQueryCache($sql, $expire = '', $path = '')
	{
		$config = self::$_config;
		if(empty($path))
		{
			if(!empty($config['queryCacheOptions']['path']) && is_dir($result = App::getPathOfAlias($config['queryCacheOptions']['path'])))
			{
				$path = $result;
			}
			else
			{
				$path = APP_PATH.'runtime'.DS.'query'.DS;//默认查询缓存目录
			}
		}
		if(empty($expire))
		{
			if(!empty($config['queryCacheOptions']['expire']))
			{
				$expire = $config['queryCacheOptions']['expire'];
			}
			else
			{
				$expire = self::$_queryCacheExpire;//默认查询缓存过期时间
			}
		}
		
		$file = $path.md5($sql);
		
		if(file_exists($file))
		{
			$d = time() - filemtime($file);
			if($d < $expire)
			{
				$content = file_get_contents($file);
				if(!empty($content))
				{
					return unserialize($content);
				}
			}
		}
		self::$_queryCacheList[$sql] = $path;//添加以便set时调用
		return NULL;
	}
	
	/**
	 * 添加一条查询缓存，路径在取时没有取到已经存到$_queryCacheList中
	 * @param string $sql 作为标识
	 * @param mixed $content 内容，数组或字符串
	 */
	public static function setQueryCache($sql, $content)
	{
		$path = self::$_queryCacheList[$sql];
		if(!is_dir($path))
		{
			$create = mkdir($path, 0777, true);
			if(!$create)
			{
				trigger_error('无法创建查询缓存文件夹：'.$path, E_USER_ERROR);
			}
		}
		$fileName = $path.md5($sql);
		$result = file_put_contents($fileName, serialize($content));
		if($result === FALSE)
		{
			trigger_error('生成查询缓存失败！', E_USER_NOTICE);
		}
		
	}
}