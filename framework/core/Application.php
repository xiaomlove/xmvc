<?php
class Application
{
	public function __get($name)
	{
		$className = ucwords($name);
		$this->$name = new $className;
		return $this->$name;
	}
	
	

	
}