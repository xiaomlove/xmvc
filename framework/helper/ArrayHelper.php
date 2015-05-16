<?php
namespace framework\helper;

class ArrayHelper
{
	/*  
	 * @param $arr array 数组，例如array('a'=>array('b'=>1, 'c'=>2))
	 * @param $keyArr array 键值数组，如array('a', 'c')，取得参数1的值2；array('a', 'b')取得1
	 * @return mixed
	 * */
	public static function getByArray($arr, $keyArr)
	{
		if(!is_array($keyArr) || !is_array($arr))
		{
			return '';
		}
		foreach($keyArr as $key)
		{
			if(isset($arr[$key])){
				if(is_array($arr[$key]))
				{
					array_shift($keyArr);
					if(count($keyArr))
					{
						return self::getByArray($arr[$key], $keyArr);
					}
					else
					{
						return $arr[$key];
					}
				}
				else
				{
					if(count($keyArr !== 1))
					{
						return '';
					}
					else 
					{
						return $arr[$key];
					}
				}
			}
		}
	}
	/**
	 * 判断是否为索引数组
	 * @param array $arr
	 * @return boolean
	 */
	public static function is_assoc($arr)
	{
		if(is_array($arr))
		{
			$keys = array_keys($arr);
			return $keys !== array_keys($keys);
		}
		return false;
	}
}