<?php
namespace framework\component;

use framework\App;

class User
{
	public $guestName = 'Guest';
	
	private static $isLogin = FALSE;
	private static $name = NULL;
	private static $id = NULL;
	private static $_instance = NULL;
	
	private function __construct()
	{
		$config = App::getConfig(array('component', 'user'));
		if(isset($config['guestName']))
		{
			$this->guestName = $config['guestName'];
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
	
	public function isLogin()
	{
		if (self::$isLogin)
		{
			return TRUE;
		}
		$loginInfo = App::ins()->session->get('loginInfo');
		if(!empty($loginInfo))
		{
			if(isset($loginInfo['expire']))
			{
				if ($loginInfo['expire'] > time())
				{
					self::$isLogin = TRUE;
				}
				else
				{
					self::$isLogin = FALSE;
				}
			}
			else
			{
				self::$isLogin = TRUE;
			}
			
		}
		else
		{
			self::$isLogin = FALSE;
		}
		return self::$isLogin;
	}
	
	public function setLogin($id, $name, $password, $remember, $expire)
	{
		$loginInfo = array('id'=>$id, 'name'=>$name, 'password'=>$password);
		if($remember && $expire > 0)
		{
			$setExpire = TRUE;
			$loginInfo['expire'] = time()+$expire;//失效时间
		}
		App::ins()->session->set('loginInfo', $loginInfo);
		if($setExpire === TRUE)
		{
			setcookie(session_name(), session_id(), time()+$expire, '/');
		}
		return TRUE;
	}
	
	public function setLogout()
	{
		return App::ins()->session->delete('loginInfo');
	}
	
	public function getName()
	{
		if (!empty(self::$name))
		{
			return self::$name;
		}
		$loginInfo = App::ins()->session->get('loginInfo');
		if (!empty($loginInfo['name']))
		{
			$name = self::$name = $loginInfo['name'];
		}
		else
		{
			$name = $this->guestName;
		}
		return $name;
	}
	
	public function getId()
	{
		if (!empty(self::$id))
		{
			return self::$id;
		}
		$loginInfo = App::ins()->session->get('loginInfo');
		if (!empty($loginInfo['id']))
		{
			$id = self::$id = $loginInfo['id'];
		}
		else
		{
			$id = '';
		}
		return $id;
	}
	
	public function setFlash($key, $value)
	{
		if(!empty($key) && is_string($key))
		{
			$key = self::getFlashPrefix().$key;//加上这个前缀
			return App::ins()->session->set($key, $value);
		}
		else
		{
			return FALSE;
		}
	}
	
	public function hasFlash($key)
	{
		if(!empty($key) && is_string($key))
		{
			$key = self::getFlashPrefix().$key;
			return App::ins()->session->_isset($key);
		}
		else
		{
			return FALSE;
		}
	}
	
	public function getFlash($key)
	{
		if(!empty($key) && is_string($key))
		{
			$key = self::getFlashPrefix().$key;
			$value = App::ins()->session->get($key);
			App::ins()->session->delete($key);//getFlash时自动删除
			return $value;
		}
		else
		{
			return NULL;
		}
	}
	/**
	 * 添加flash的前缀，这样子看只对同模块同控制器同一个用户有效，需要跨控制器的flash比较少吧。
	 * Enter description here ...
	 */
	private function getFlashPrefix()
	{
		return md5(strval(MODULE).strval(CONTROLLER).App::ins()->user->getId());
	}
	
	
	
}