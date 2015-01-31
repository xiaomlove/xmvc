<?php
final class Router
{
	private static $_config = NULL;
	private static $_host = NULL;
	private static $_scriptName = NULL;//均不含左右的'/'
	private static $_showScriptName = TRUE;
	private static $_separator = '/';
	private static $_urlSuffix = NULL;
	
	private static $_requestUri = NULL;
	private static $_queryString = NULL;
	private static $_offsetUri = '';//路径不在网站根目录时前边多余部分
	
	public static function parseUrl()
	{
		self::_init();
		$config = self::$_config;
		
		//echo 'query_string:';
		//var_dump(self::$_queryString);
		//echo '<br/>request_uri:';
		//var_dump(ltrim(self::$_requestUri, '/'));
		//echo '<br/>scriptName:';
		//echo self::$_scriptName;
		//echo '<br/>host:';
		//echo self::$_host;
		
		$separator = self::$_separator;
		
		$requestUri = trim(self::$_requestUri, '/');
		if(empty($requestUri))
		{
			goto A;
		}
//		echo '<br/>之前：';
//		var_dump($requestUri);
		//有脚本文件，去掉即为正确的需要匹配的部分
		if(strpos($requestUri, self::$_scriptName) !== FALSE)
		{
			$len = strlen(self::$_scriptName);
			$requestUri = substr($requestUri, $len+1);
		}
		else//没有脚本文件，如果入口不在网站根目录，需要去掉路径差才是正确的需要匹配的部分 
		{
			$lastPos = strrpos(self::$_scriptName, '/');
			if($lastPos !== FALSE)
			{
				$offsetPart = self::$_offsetUri = substr(self::$_scriptName, 0, $lastPos);
// 				var_dump($offsetPart).'<br/>';
				$requestUri = str_replace($offsetPart, '', $requestUri);
			}
		}
		
//		echo '</br>之后：';
// 		var_dump($requestUri);
		
		//去掉脚本文件后为空，
		A:
		$cacheConfig = App::getConfig('cache');
		if(!empty($cacheConfig))
		{
			Cache::init($cacheConfig);
			if(isset($cacheConfig['pageCache']) && $cacheConfig['pageCache'])
			{
				//检查url为标识的缓存，不需要解析路由
				$url = empty($requestUri) ? '/' : $requestUri;
				$cache = Cache::checkPageCacheByUrl($url);
				if($cache !== NULL)
				{
					echo $cache;
					exit;
				}
			}
			
		}
		
		if(!$requestUri)
		{
			self::_defineMVC('Module');
			self::_defineMVC('Controller');
			self::_defineMVC('Action');
			
		}
		else //按模式解析Url
		{
			$mode = isset($config['mode'])? $config['mode']: 'normal';
			if($mode === 'path')
			{
				if(!isset($config['rules']) || !count($config['rules']))
				{
					trigger_error('path模式必须设置rules', E_USER_ERROR);//手动触发必须都是USER级别
				}
				else 
				{
					self::_parseRules($config, $requestUri);
				}
			}
			elseif($mode === 'normal')
			{
				self::_parseMVC();
			}
			else 
			{
				trigger_error('设置的路由模式无效', E_USER_ERROR);
			}
		}
		
		//路由解析完毕，检查非url为标识的缓存 && !Cache::$_urlMatch
		if(!empty($cacheConfig) && isset($cacheConfig['pageCache']) && $cacheConfig['pageCache'] && !Cache::$_urlMatch)
		{
			$cache = Cache::checkPageCacheByMVCP();
		}
		
	
		self::_MagicQuotesStrip();
		//echo '<br/>';
		//echo '<br/>';
		//echo '$_GET:';
		//var_dump($_GET);
		//echo '<br/>';
		//echo '<h4>module:'.MODULE.'</h4>';
		//echo '<h4>controller:'.CONTROLLER.'</h4>';
		//echo '<h4>action:'.ACTION.'</h4>';
		
		//echo '<br/>';
// 		self::_createUrl('thread/show', array('tid'=>'2', 'perpage'=>'1', 'ss'=>88));
		//echo '<pre>';
		//var_dump($_SERVER);
		
	}
	
	private static function _init()
	{
		self::$_host = $_SERVER['HTTP_HOST'];
		self::$_scriptName = trim($_SERVER['SCRIPT_NAME'], '/');
		self::$_queryString = $_SERVER['QUERY_STRING'];
		self::$_requestUri = $_SERVER['REQUEST_URI'];
		
		$config = self::$_config = App::getConfig('router');
		if(isset($config['showScriptName']) && $config['showScriptName'])
		{
			self::$_showScriptName = TRUE;
		}
		else
		{
			self::$_showScriptName = FALSE;
		}
		if(isset($config['separator']))
		{
			self::$_separator = $config['separator'];
		}
		
	}
	
	private static function _parseRules($config, $requestUri)
	{
		$rules = $config['rules'];
		$separator = self::$_separator;
		
		//有query_string去掉
		$requestUri = strtolower(trim(str_replace('?'.self::$_queryString, '', $requestUri), '/'));//转化为小写
		$separatorCount = substr_count($requestUri, $separator);//分割符个数
		foreach($rules as $key=>$value)
		{
			$key = strtolower(trim($key, '/'));
// 			$value = trim($value, '/');
// 			//echo '<br/>key:',$key,'----requestUri:',$requestUri,'<br/>';
			
			if($key === $requestUri)//直接相等，不会有参数添加到$_GET，不用去掉requestUri的后缀
			{
				if(is_array($value) && count($value) >= 1)
				{
					if(isset($value['urlSuffix']))
					{
						self::$_urlSuffix = $value['urlSuffix'];//后缀
					}
					self::_parseRuleValue(strtolower(trim($value[0], '/')));
				}
				elseif(is_string($value))
				{
					self::_parseRuleValue(strtolower(trim($value, '/')));
				}
				else 
				{
					trigger_error('rules配置不正确，值为字符串或数组', E_USER_ERROR);
					continue;
				}
				break;
			}
			elseif(substr_count($key, $separator) === $separatorCount)//不相等，需要正则匹配找出变量名和变量值
			{
				$paramReg = '/<(.+)(:|>)/U';
				preg_match_all($paramReg, $key, $paramMatches);
				if(!isset($paramMatches[1]) || empty($paramMatches[1]) || !isset($paramMatches[2]) || empty($paramMatches[2]))
				{
					//echo "<br/>".htmlspecialchars($key)."——count===但无法匹配<br/>";
					continue;
				}
				$paramArr = $paramMatches[1];
				$endCharacterArr = $paramMatches[2];
				//echo '<br/>paramArr:';
				//var_dump($paramArr);
				//echo '<br/>endCharacterArr:';
				//var_dump($endCharacterArr);
				if(count($paramArr) !== count($endCharacterArr))
				{
					//echo "<br/>".htmlspecialchars($key)."——无法匹配，param与endCharacterArr个数不一致<br/>";
					continue;
				}
				
				$search = array('/', '>');
				$replace = array('\/', ')');
			
				foreach($paramArr as $index=>$param)
				{
					if(strpos($param, $separator) !== FALSE)
					{
						trigger_error('rules配置有误，每个参数需用<>包起来', E_USER_ERROR);
						//echo "<br/>".htmlspecialchars($key)."——无法匹配,rules配置有误，参数需用<>包起来<br/>";
						continue 2;
					}
					if($endCharacterArr[$index] === ':')//有正则表达式，把变量去掉即可
					{
						array_push($search, '<'.$param.':');
						array_push($replace, '(');
					}
					else
					{
						array_push($search, '<'.$param);
						array_push($replace, '(.+');//没有正则，变量替换为正则
					}
				}
				
				$key = str_replace($search, $replace, $key);
				$keyReg = '/'.$key.'/';
				
				if(is_array($value))
				{
					if(isset($value['urlSuffix']) && !empty($value['urlSuffix']))
					{
						self::$_urlSuffix = $value['urlSuffix'];//后缀
						//有后缀，去掉
						if(strpos($requestUri, $value['urlSuffix']) !== FALSE)
						{
							$uriLen = strlen($requestUri);
							$suffixLen = strlen($value['urlSuffix']);
								
							$requestUri = substr($requestUri, 0, $uriLen-$suffixLen);
						}
					}	
				}
				preg_match_all($keyReg, $requestUri, $valueMatches, PREG_SET_ORDER );
				//echo '<br/>valueMatches:';
				//var_dump($valueMatches);
				if( empty($valueMatches) || (count($paramArr) !== count($valueMatches[0])-1))
				{
					//echo "<br/>".htmlspecialchars($key)."——无法匹配,参数个数与值个数不一致<br/>";
					continue;
				}
				$paramValueArr = $valueMatches[0];
				array_shift($paramValueArr);
				//echo '<br/>paramValueArr:';
				//var_dump($paramValueArr);
				
				if(is_array($value))
				{
					$mvc = trim($value[0], '/');
					self::_parseRuleValue(strtolower($mvc));//定义MVC有必要全部先转换为小写，再大写首字母，其他情况是什么就什么，严格区分
				}
				elseif(is_string($value))
				{
					self::_parseRuleValue(strtolower(trim($value, '/')));
				}
				else
				{
					trigger_error('rules配置不正确，值为字符串或数组', E_USER_ERROR);
					continue;
				}
				$new = array_combine($paramArr, $paramValueArr);
				if(App::ins()->request->isGet())
				{
					$_GET = array_merge($_GET, $new);
				}
				elseif(App::ins()->request->isPost())
				{
					$_POST = array_merge($_POST, $new);	
				}
				else 
				{
					trigger_error(E_USER_ERROR, '非法请求');
					exit();
				}
				
				break;
				
			}
			else 
			{
				//echo "<br/>".htmlspecialchars($key)."——无法匹配<br/>";
			}
			
			
		}
		
	}
	
	public static function createUrl($mvc = '', $options = array())
	{
		if(empty($mvc) || !is_string($mvc) || !is_array($options))
		{
			trigger_error('参数有误！', E_USER_ERROR);
			return;
		}
		if($mvc === '/')
		{
			return '/';
		}
		
		$mvc = trim($mvc, '/');
		
		//分路由模式讨论
		if(!isset(self::$_config['mode']) || self::$_config['mode'] === 'normal')
		{
			$requeryUri = '?';
			if(strpos($mvc, '/') === FALSE)
			{
				$requeryUri .= 'c='.$mvc;
			}
			elseif(substr_count($mvc, '/') === 1)
			{
				$mvcArr = explode('/', $mvc);
				$requeryUri .= 'c='.$mvcArr[0].'&a='.$mvcArr[1];
			}
			elseif(substr_count('/', $mvc) === 2)
			{
				$mvcArr = explode('/', $mvc);
				$requeryUri .= 'm='.$mvcArr[0].'&c='.$mvcArr[1].'&a='.$mvcArr[2];
			}
			else 
			{
				trigger_error('createUrl参数错误，最多mvc三个', E_USER_ERROR);
				return;
			}
			if(!empty($options))
			{
				foreach($options as $lastKey=>$lastValue)
				{
					$requeryUri .= '&'.$lastKey.'='.$lastValue;
				}
					
			}
			unset($options);
			if(self::$_config['showScriptName'])
			{
				$requeryUri = '/'.self::$_scriptName.'/'.$requeryUri;
			}
			
		}
		elseif(isset(self::$_config['mode']) && self::$_config['mode'] === 'path')
		{
			$rules = self::$_config['rules'];
			foreach($rules as $ruleKey=>$ruleValue)
			{
				$ruleKey = trim($ruleKey, '/');
				if($ruleKey === $mvc)//跟rules左边相等，直接就它了
				{
					$requeryUri = $ruleKey;
					if(self::$_config['showScriptName'])
					{
						$requeryUri = '/'.self::$_scriptName.'/'.$requeryUri;
					}
					else 
					{
						$requeryUri = '/'.$requeryUri;
					}
					
					if(is_array($ruleValue) && isset($ruleValue['urlSuffix']))
					{
						$requeryUri .= $ruleValue['urlSuffix'];
					}
					break;
				}
				else
				{
					if(is_array($ruleValue))
					{
						$ruleValueMVC = $ruleValue[0];
						
					}
					elseif(is_string($ruleValue))
					{
						$ruleValueMVC = $ruleValue;
					}
					else 
					{
						//echo htmlspecialchars($ruleKey).'无法匹配4</br>';
						continue;
					}
					
					
					if($mvc === trim($ruleValueMVC))
					{
						if(strpos($ruleKey, '<') === FALSE)//规则中没有参数传递
						{
							$requeryUri = $ruleKey;//ruleValue=mvc,就是ruleKey了
							
							if(self::$_config['showScriptName'])
							{
								$requeryUri = '/'.self::$_scriptName.'/'.$requeryUri;
							}
							else 
							{
								$requeryUri = '/'.$requeryUri;
							}
							
							if(is_array($ruleValue) && isset($ruleValue['urlSuffix']))
							{
								$requeryUri .= $ruleValue['urlSuffix'];
							}
							
							break;
						}
						else//解析传递的参数，将ruleKey中能解析的放上，其他以普通形式传递
						{
							if(!empty($options))
							{
								$requeryUri = $ruleKey;
								$allOptions = $options;
								foreach($options as $optionKey=>&$optionValue)
								{
									if(strpos($ruleKey, '<'.$optionKey) !== FALSE)
									{
										$reg = '/<'.$optionKey.'(.*)>/U';
// 										//var_dump($reg);
// 										
										$requeryUri = preg_replace($reg, $optionValue, $requeryUri);
										
										unset($options[$optionKey]);
									}
								}
								if(strpos($requeryUri, '<') !== FALSE)//无法把所有参数替换完，不对
								{
									//echo htmlspecialchars($ruleKey).'无法匹配1</br>';
									$options = $allOptions;
									continue;
								}
								else
								{
									if(self::$_config['showScriptName'])
									{
										$requeryUri = '/'.self::$_scriptName.'/'.$requeryUri;
									}
									else
									{
										$requeryUri = '/'.$requeryUri;
									}
									if(is_array($ruleValue) && isset($ruleValue['urlSuffix']))
									{
										$requeryUri .= $ruleValue['urlSuffix'];
									}
									break;
								}
							}
							else
							{
								//echo htmlspecialchars($ruleKey).'无法匹配2</br>';
								continue;
							}
						}
					}
					else
					{
						//echo htmlspecialchars($ruleKey).'无法匹配3</br>';
						continue;
					}
				}
				
			}
			if(strpos($requeryUri, '<') !== FALSE)
			{
				trigger_error('无法创建url,匹配不了规则', E_USER_ERROR);
				return;
			}
			if(!empty($options))
			{
				$requeryUri .= '?';
				foreach($options as $lastKey=>$lastValue)
				{
					$requeryUri .= $lastKey.'='.$lastValue.'&';
				}
					
			}
			unset($options);
			
		}
// 		echo '生成的url暂时是:';
// 		var_dump($requeryUri);
		if(!empty(self::$_offsetUri))
		{
			$requeryUri .= '/'.self::$_requestUri;
		}
		return rtrim($requeryUri, '&');//从网站根目录开始
	}
	
	private static function _parseRuleValue($value)
	{
		$separator = '/';//rules值都是用的/分割，key是可以换
		
		if(strpos(trim($value, '/'), $separator) === FALSE)//只有一个参数,认为是controller
		{
			self::_defineMVC('Module');
			define('CONTROLLER', ucwords($value));
			self::_defineMVC('Action');
		}
		else
		{
			$paramArr = explode($separator, $value);
			if(count($paramArr) === 2)//两个参数认为是controller和action
			{
				self::_defineMVC('Module');
				define('CONTROLLER', ucwords($paramArr[0]));
				define('ACTION', ucwords($paramArr[1]));
			}
			elseif(count($paramArr) === 3)//三个参数认为分别是module,controller和action
			{
				define('MODULE', ucwords($paramArr[0]));
				define('CONTROLLER', ucwords($paramArr[1]));
				define('ACTION', ucwords($paramArr[2]));
			}
			else
			{
				trigger_error('路由rules右边值MVC最多3个参数', E_USER_ERROR);
			}
		}
	}
	
	/*为normal模式时  */
	private static function _parseMVC()
	{
		$queryStr = self::$_queryString;//一样被加上魔术引号
		parse_str($queryStr, $queryArr);
		self::_MagicQuotesStrip($queryArr);
		
		$keyArr = array_keys($queryArr);
		if(isset($keyArr[0]) && $keyArr[0] === 'm')
		{
			define('MODULE', ucwords($queryArr['m']));
		}
		else 
		{
			self::_defineMVC('Module');
			
		}
		
		if(isset($keyArr[1]) && $keyArr[1] === 'c')
		{
			define('CONTROLLER', ucwords($queryArr['c']));
		}
		else
		{
			self::_defineMVC('Controller');
		}
		
		if(isset($keyArr[2]) && $keyArr[2] === 'a')
		{
			define('ACTION', ucwords($queryArr['a']));
		}
		else
		{
			self::_defineMVC('Action');
		}
		
	}
	
	//去除魔术引号
	private static function _MagicQuotesStrip(&$array = '')
	{
		if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
		{
			if(is_array($array))
			{
				$array = self::_stripSlashes($array);
				return;
			}
			if(isset($_GET))
			{
				$_GET = self::_stripSlashes($_GET);
				
			}
			elseif(isset($_POST))
			{
				$_POST = self::_stripSlashes($_POST);
			}
			elseif(isset($_REQUEST))
			{
				$_REQUEST = self::_stripSlashes($_REQUEST);
			}
			elseif(isset($_COOKIE))
			{
				$_COOKIE = self::_stripSlashes($_COOKIE);
			}
		}
	}
	
	//递归调用stripslashes()
	private static function _stripSlashes(&$data)
	{
		if(is_array($data))
		{
			if(count($data) === 0)
			{
				return $data;
			}
			$keys = array_map('stripslashes', array_keys($data));
			$data = array_combine($keys, array_values($data));
			return array_map(array('Router', '_stripSlashes'), $data);
		}
		else 
		{
			return stripslashes($data);
		}
	}
	
	private static function _defineMVC($string)
	{
		$default = strtolower(App::getConfig('default'.$string));
		if(!empty($default) && is_string($default))
		{
			define(strtoupper($string), ucwords($default));
		}
		else
		{
			define(strtoupper($string), NULL);
		}
	}
	
	public static function getOffsetUri()
	{
		return self::$_offsetUri;
	}
	
}