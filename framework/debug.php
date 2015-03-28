<?php

$arr = array('Df', 'YY');
$s = array_flip($arr);
var_dump($s);
$s = array_change_key_case($s);
var_dump($s);
$b = array_flip($s);
var_dump($b);

