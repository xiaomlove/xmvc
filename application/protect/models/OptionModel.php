<?php
namespace application\protect\models;

use framework;
use framework\App;

class OptionModel extends \framework\core\Model
{
	
	public function tableName()
	{
		return 'option';
	}
	
	//子类获得模型对象的方法，通过调用父类的getModel()，传递子类的类名
	public static function model($className = __CLASS__)
	{
		return parent::getModel($className);
	}
	
	public function get($key = '')
	{
		$doCache = FALSE;
		if (App::isComponentEnabled('Memcache'))
		{
			if (empty($key))
			{
				$key = 'all_options';
			}
			$cacheKey = json_encode($key);
			$result = App::ins()->mem->get($cacheKey);
			if ($result !== FALSE)
			{
				return $result;
			}
			$doCache = TRUE;
		}
		if (empty($key))
		{
			$result = $this->select();
		}
		elseif(is_array($key))
		{
			$str = "(";
			foreach ($key as $oneKey)
			{
				$str .= "'".$oneKey."',";
			}
			$str = rtrim($str, ',').")";
			$result = $this->where("option_key IN $str")->select();
		}
		else
		{
			$result = $this->where("option_key='$key'")->limit(1)->select();
		}
		
		if (empty($result))
		{
			$result  = '';
		}
		elseif(count($result == 1))
		{
			$result = $result[0]['option_value'];
		}
		else
		{
			$data = array();
			foreach ($result as $option)
			{
				$data[$option['option_key']] = $option['option_value'];
			}
			$result = $data;
		}
		if ($doCache)
		{
			App::ins()->mem->set($cacheKey, $result);
		}
		return $result;
	}
	
	
	
}