<?php
class Validator
{
	public static $_validators = array();
	private static $_rules = array();
	private static $_data = array();
	
	public static function init($rules, $data)
	{
		if(empty(self::$_rules))
		{
			self::$_rules = $rules;
		}
		if(empty(self::$_data))
		{
			self::$_data = $data;
		}

	}
	
	/**
	 * 验证非空
	 * @param unknown $field
	 * @return boolean
	 */
	public static function required($field)
	{
		$result = trim(self::$_data[$field]);
		return !empty($result);
	}
	
	/**
	 * 验证非负整数
	 * @param unknown $field
	 * @return boolean
	 */
	public static function number($field)
	{
		$required = self::required($field);
		if($required)
		{
			$value = self::$_data[$field];
			return ctype_digit(strval($value));
		}
		else 
		{
			return FALSE;
		}
	}
	/**
	 * 验证浮点数
	 * @param unknown $field
	 * @return boolean
	 */
	public static function float($field)
	{
		$required = self::required($field);
		if($required)
		{
			$value = self::$_data[$field];
			if (preg_match('/^(\s)/', $value))
			{
				return FALSE;
			}
			return is_numeric($value);
		}
		else
		{
			return FALSE;
		}
	}
	
	
	/**
	 * 验证在某个数组内
	 * @param unknown $field
	 * @param unknown $rule
	 * @return boolean
	 */
	public static function in($field, $rule)
	{
		if(!empty($rule[3]) && is_array($rule[3]))
		{
			return in_array(self::$_data[$field], $rule[3]);
		}
		else 
		{
			trigger_error('"in"规则必须指定第四个参数为附加条件，为一个包括所有可能值的数组', E_USER_WARNING);
			return FALSE;
		}
	}
	
	/**
	 * 验证字符串长度
	 * @param unknown $field
	 * @param unknown $rule
	 * @return boolean
	 */
	public static function length($field, $rule)
	{
		if(!empty($rule[3]) && is_array($rule[3]))
		{
			$flag = TRUE;
			if(!empty($rule[3]['min']))
			{
				$flag = $flag && (StringHelper::getStrLength(self::$_data[$field])>=$rule[3]['min']);
			}
			if(!empty($rule[3]['max']))
			{
				$flag = $flag && (StringHelper::getStrLength(self::$_data[$field])<=$rule[3]['max']);
			}
			return $flag;
		}
		else 
		{
			trigger_error('"length"规则必须指定第四个参数为附加条件，为一个包含最大最小值的数组(可以只有其中一个)', E_USER_WARNING);
			return FALSE;
		}
	}
	
	/**
	 * 验证数值范围
	 * @param unknown $field
	 * @param unknown $rule
	 * @return boolean
	 */
	public static function range($field, $rule)
	{
		if(!empty($rule[3]) && is_array($rule[3]))
		{
			$flag = TRUE;
			if(!empty($rule[3]['min']))
			{
				$flag = $flag && intval(self::$_data[$field]) >= intval($rule[3]['min']);
			}
			if(!empty($rule[3]['max']))
			{
				$flag = $flag && intval(self::$_data[$field]) <= intval($rule[3]['max']);
			}
			return $flag;
		}
		else 
		{
			trigger_error('"range"规则必须指定第四个参数为附加条件，为一个包含最大最小值的数组(可以只有其中一个)', E_USER_WARNING);
			return FALSE;
		}
	}
	
	/**
	 * 跟其中某个字段对比，必须相等
	 * @param unknown $field
	 * @param unknown $rule
	 * @return boolean
	 */
	public static function equate($field, $rule)
	{
		if(!empty($rule[3]) && is_string($rule[3]) && isset(self::$_data[$rule[3]]))
		{
			return self::$_data[$field] === self::$_data[$rule[3]];
		}
		else 
		{
			trigger_error('"equate"规则必须指定第四个参数为附加条件，为字段中的任意一个', E_USER_WARNING);
			return FALSE;
		}
	}
	
	/**
	 * 跟其中某个字段相比，必须不相等
	 * @param unknown $field
	 * @param unknown $rule
	 * @return boolean
	 */
	public static function notEquate($field, $rule)
	{
		if(!empty($rule[3]) && is_string($rule[3]) && isset(self::$_data[$rule[3]]))
		{
			return self::$_data[$field] !== self::$_data[$rule[3]];
		}
		else
		{
			trigger_error('"notEquate"规则必须指定第四个参数为附加条件，为字段中的任意一个', E_USER_WARNING);
			return FALSE;
		}
	}
}