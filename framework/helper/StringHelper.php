<?php
namespace framework\helper;

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
	
	public static function encodeFileName($str)
	{
		if(substr(PHP_OS, 0, 3) === 'WIN')
		{
			return mb_convert_encoding($str, 'GBK', 'UTF-8,GBK,GB2312,BIG5');
		}
		else 
		{
			return $str;
		}
	}
}