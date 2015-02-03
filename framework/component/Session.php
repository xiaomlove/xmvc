<?php
class Session
{
	public function __construct()
	{
		if(session_id() === '')
		{
			session_start();
		}
	}
	
	public function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}
	
	public function _isset($key)
	{
		return isset($_SESSION[$key]);
	}
	
	public function get($key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
	}
	
	public function delete($key)
	{
		unset($_SESSION[$key]);
	}
	
	public function destroy()
	{
		$_SESSION = array();
		if(ini_get('session.use_cookies'))
		{
			setcookie(session_name(), '', time()-3600, '/');
		}
		return session_destroy();
	}
}