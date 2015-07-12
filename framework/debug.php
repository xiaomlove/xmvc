<?php

$a = array(
		'a' => '   b',
		'1' => array(
				'c' => 'dd      ',
		),
);

array_walk_recursive($a, 'myTrim');

function myTrim(&$param)
{
	$param = trim($param);
}

var_dump($a);
