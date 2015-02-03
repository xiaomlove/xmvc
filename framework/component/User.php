<?php
class User
{
	public $guestName = 'Guest';
	
	private $isLogin = FALSE;
	private $name = NULL;
	private $id = NULL;
	
	public function __construct()
	{
		$config = App::getConfig(array('component', 'user'));
		if(isset($config['guestName']))
		{
			$this->guestName = $config['guestName'];
		}
	}
	public function isLogin()
	{
		$loginInfo = App::ins()->session->get('loginInfo');
		if(!empty($loginInfo))
		{
			if(isset($loginInfo['expire']))
			{
				return $loginInfo['expire'] > time();
			}
			return TRUE;
		}
		return FALSE;
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
		return App::ins()->session->destroy();
	}
	
	public function getName()
	{
		$loginInfo = App::ins()->session->get('loginInfo');
		return empty($loginInfo) ? NULL : $loginInfo['name'];
	}
	
	public function getId()
	{
		$loginInfo = App::ins()->session->get('loginInfo');
		return empty($loginInfo) ? NULL : $loginInfo['id'];
	}
	
	
	
}