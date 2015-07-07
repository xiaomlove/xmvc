<?php
namespace framework\core;

interface CacheInterface
{
	public function set($key, $var, $expire);
	
	public function get($key);
	
	public function delete($key);
	
	public function clear();
	
	public function increase($key, $value = 1);
	
	public function decrease($key, $value = 1);
	
	public function getKeys();
}