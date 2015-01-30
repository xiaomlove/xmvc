<?php
class StringHelper
{
	/**
	 * 获得字符串的长度，一个中文一个英文都是算一个
	 * @param unknown $str
	 * @return mixed|number
	 */
	public static function getStrLength($str)
	{
		if(function_exists('mb_strlen'))
		{
			return mb_strlen($str, 'utf-8');
		}
		elseif(function_exists('iconv_strlen'))
		{
			return iconv_strlen($str, 'utf-8');
		}
		else
		{
			preg_match_all('/./us', $str, $match);
			return count($match[0]);	
		}
	}
}