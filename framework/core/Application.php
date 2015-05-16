<?php
namespace framework\core;

class Application
{
	public function __get($name)
	{
		$className = 'framework\component\\'.ucwords($name);
		$this->$name = new $className;
		return $this->$name;
	}	
}