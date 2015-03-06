<?php

final class App
{
	private static $_config = NULL;
	private static $_debug = FALSE;
	private static $_ins = NULL;
	private static $_requirePath = array();//额外的引入路径
	
	
	public static function run($config)
	{
		self::setPath();
		spl_autoload_register(array('App', 'appAutoload'));
		
		if(defined('DEBUG') && (DEBUG === 1 || DEBUG === true))
		{
			self::$_debug = TRUE;
			Log::start();
			Log::requireFile(__FILE__);
			register_shutdown_function('Log::outPutLog');
		}
		self::$_config = $config;	
			
		self::setErrorHandler();
		
		self::$_ins = new Application();
//		echo '<pre>';
//		var_dump($_POST);exit;
		

		Router::parseUrl();
		
		self::runController();
	}
	
	public static function ins()
	{
		return self::$_ins;
	}
	
	
	private static function setPath()
	{
		define('DS', DIRECTORY_SEPARATOR);
		defined('APP_PATH') or define('APP_PATH', dirname(__DIR__).DS.'application'.DS);
		define('CORE_PATH', __DIR__.DS.'core'.DS);
		define('LIB_PATH', __DIR__.DS.'lib'.DS);
		define('HELPER_PATH', __DIR__.DS.'helper'.DS);
		define('COM_PATH', __DIR__.DS.'component'.DS);
	}
	
	private static function setErrorHandler()
	{
		if(self::$_debug)
		{
			error_reporting(E_ALL | E_STRICT);//所有错误都报告
			ini_set('display_errors', 'On');
			set_error_handler(array('Log', 'errorCatch'), E_ALL | E_STRICT);
		}
		else
		{
			ini_set('display_errors', 'Off');
			ini_set('log_errors', 'On');
			ini_set('error_log', APP_PATH.'runtime'.DS.'error_log' );
		}
	}
	/**
	 * 重名还是会有问题，看来得把命名空间加上。这样子的引入显得很Low
	 */
	private static function appAutoload($className)
	{
		//核心文件夹
		if(file_exists($file = CORE_PATH.$className.'.php'))
		{
			require $file;
			if(self::$_debug)
			{
				Log::requireFile($file);
			}
		}
		//控制器
		elseif(substr($className, -10) === 'Controller')
		{
			if(MODULE === NULL)
			{
				$file = APP_PATH.'protected'.DS.'controllers'.DS.$className.'.php';
			}
			else
			{
				$file = APP_PATH.'protected'.DS.'modules'.DS.strtolower(MODULE).DS.'controllers'.DS.$className.'.php';
			}
			if (!file_exists($file))
			{
				//分模块时如果找不到，再回头找
				$file = APP_PATH.'protected'.DS.'controllers'.DS.$className.'.php';
			}
			require $file;
			if(self::$_debug)
			{
				Log::requireFile($file);
			}
		}
		//模型
		elseif(substr($className, -5) === 'Model')
		{
			$file = APP_PATH.'protected'.DS.'models'.DS.$className.'.php';
			require $file;
			if(self::$_debug)
			{
				Log::requireFile($file);
			}
		}
		//帮助类
		elseif(substr($className, -6) === 'Helper')
		{
			$file = HELPER_PATH.$className.'.php';
			require $file;
			if(self::$_debug)
			{
				Log::requireFile($file);
			}
		}
		//组件文件夹
		elseif(file_exists($file = COM_PATH.$className.'.php'))
		{
			require $file;
			if(self::$_debug)
			{
				Log::requireFile($file);
			}
		}
		//LIB文件夹
		elseif(file_exists($file = LIB_PATH.$className.'.php'))
		{
			require $file;
			if(self::$_debug)
			{
				Log::requireFile($file);
			}
		}
		//临时额外引入的文件
		elseif(!empty(self::$_requirePath))
		{
			foreach(self::$_requirePath as $path)
			{
				$file = $path.$className.'.php';
				if(file_exists($file))
				{
					require $file;
					if(self::$_debug)
					{
						Log::requireFile($file);
					}
					return;
				}
				
			}
		}
		
	}
	
	public static function addRequirePath($path)
	{
		if(is_dir($path) && !in_array($path, self::$_requirePath))
		{
			self::$_requirePath[] = $path;
		}
		else 
		{
			trigger_error('不是路径：'.$path, E_USER_ERROR);
		}
		
	}
	
	public static function getConfig($key = '')
	{
		if($key === '')
		{
			return self::$_config;
		}
		elseif(is_string($key) && isset(self::$_config[$key]))
		{
			return self::$_config[$key];
		}
		elseif(is_array($key))
		{
			return ArrayHelper::getByArray(self::$_config, $key);
		}
		return '';
	}
	
	public static function setConfig($key, $value)
	{
		if(is_string($key))
		{
			self::$_config[$key] = $value;
		}
	}
	
	public static function getPathOfAlias($alias)
	{
		if(empty($alias) || !is_string($alias))
		{
			return FALSE;
		}
		
		$alias = str_replace(array('/', '\\'), '', $alias);
		$alias = trim($alias);//是不是太多余了？
		$prefix = substr($alias, 0, strpos($alias, '.'));
		$aliasList = array('application', 'framework');
		$alias = trim(str_replace($prefix, '', $alias), '.');//去掉别名自身
		if(in_array($prefix, $aliasList))
		{
			switch ($prefix)
			{
				case 'application':
					$path = APP_PATH.str_replace('.', DS, $alias).DS;
					break;
				case 'framework':
					$path = __DIR__.DS.str_replace('.', DS, $alias).DS;
					break;
			}
			return $path;
		}
		else 
		{
			trigger_error('该路径别名不存在：'.$prefix, E_USER_ERROR);
			return '';
		}
	}
	
	private static function runController()
	{
		
		if(!defined('CONTROLLER') || CONTROLLER === NULL)
		{
			trigger_error('没有定义控制器！', E_USER_ERROR);
			return;
		}
		if(!defined('ACTION') || ACTION === NULL)
		{
			trigger_error('没有定义方法！', E_USER_ERROR);
			return;
		}
		$controllerName = CONTROLLER.'Controller';
		$controller = new $controllerName;
		$actionName = 'action'.ACTION;
		if(method_exists($controller, $actionName))
		{
			call_user_func(array($controller, $actionName));
		}
		else 
		{
			trigger_error('没有找到方法：'.ACTION, E_USER_ERROR);
		}
	}
	
	public static function isDebug()
	{
		return self::$_debug;
	}
}