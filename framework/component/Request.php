<?php
class Request
{
	public function isAjax()
	{
		//这是jQuery自动添加的请求头，因此只对jQuery发起的ajax判断准确
		//原生ajax请求也得添加这个请求头才能准确判断。否则自行根据发送的请求头判断
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}
	
	public function isPost()
	{
		return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
	}
	
	public function isGet()
	{
		return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET';
	}
	
	public function getServerIP()
	{
		return isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : 'unknown';
	}
	
	public function getClientIP()
	{
		$ip = 'unknown';
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
		{
			$ip = getenv('HTTP_CLIENT_IP');
		}
		elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
		{
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	public function getBaseUrl()
	{
		$url = self::getHttpType().'://'.$_SERVER['HTTP_HOST'].'/';
		$offset = Router::getOffsetUri();
		if(!empty($offset))
		{
			$url .= $offset.'/';
		}
		return $url;
	}
	
	public function getHttpType()
	{
		if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'))
		{
			return 'https';
		}
		else
		{
			return 'http';
		}
	}
	
	
	
}