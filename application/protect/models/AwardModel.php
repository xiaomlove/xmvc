<?php
namespace application\protect\models;

// use framework\App;

class AwardModel extends \framework\core\Model
{
	const TYPE_SYSTEM = 1;//系统奖励
	
	const TYPE_USER = 2;//用户奖励
	
	private static $_systemValue;
	
	private static $_userValue;
	
	public function init()
	{
		self::$_systemValue = array(3);
		self::$_userValue = array(100, 200, 500, 1000);
	}

	public function tableName()
	{
		return 'award';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	public function getTypeInfo($type = '')
	{
		$allType = array(
				self::TYPE_SYSTEM => array('typeValue' => self::TYPE_SYSTEM, 'typeName' => '系统奖励'),
				self::TYPE_USER => array('typeValue' => self::TYPE_USER, 'typeName' => '用户奖励'),
		);
		if (empty($type))
		{
			return $allType;
		}
		return isset($allType[$type]) ? $allType[$type] : NULL;
	}
	
	public function getAllType()
	{
		return array_keys(self::getTypeInfo());
	}
	
	public function checkTypeValue($type, $value)
	{
		$type = intval($type);
		$value = intval($value);
		if ($type === self::TYPE_SYSTEM)
		{
			return in_array($value, self::$_systemValue);
		}
		elseif ($type === self::TYPE_USER)
		{
			return in_array($value, self::$_userValue);
		}
		else
		{
			return FALSE;
		}
	}
	
	public function getList($torrentId)
	{
// 		$data = $this->where(array('torrent_id' => $torrentId))->order('id ASC')->select();
		$sql = "SELECT a.*,b.name as user_name FROM `award` a LEFT JOIN `user` b ON a.user_id=b.id WHERE torrent_id=$torrentId ORDER BY a.id ASC";
		$data = $this->findBySql($sql);
		if (empty($data))
		{
			return NULL;
		}
		$out = array();
		$sum = 0;
		foreach ($data as $award)
		{
			if ($award['type'] == self::TYPE_SYSTEM)
			{
				$out[self::TYPE_SYSTEM][] = $award;
			}
			elseif ($award['type'] == self::TYPE_USER)
			{
				$out[self::TYPE_USER][] = $award;
				$sum += $award['value'];
			}
		}
		$out['sum'] = $sum;
		return $out;
	}
}