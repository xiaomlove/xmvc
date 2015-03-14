<?php

$a = array(
		'abc@badidu.com',
		'efg#baidu.com',   
		'fejifj[at]baidu.com',
		'ssb@baidu.com.cn'
		);

$reg = '/(.*)(@|#|\[at\])baidu.com$/';

foreach ($a as $value)
{
	if(preg_match($reg, $value, $match))
	{
		var_dump($match);
	}
	
}



