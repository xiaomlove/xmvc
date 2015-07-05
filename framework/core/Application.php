<?php
namespace framework\core;

class Application
{
	public function __get($name)
	{
		$className = 'framework\component\\'.ucwords($name);
		$this->$name = $className::getInstance();
		return $this->$name;
	}	
}