<?php
namespace application\protect\models;

//楼栋模型

class CommentBuildingModel extends \framework\core\Model
{
	public function tableName()
	{
		return 'comment_building';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	
	public function getMaxPosition()
	{
		$sql = "SELECT max(`position`) as maxPosition FROM ".self::tableName();
		$result = $this->getOneBySql($sql);
		return empty($result) ? 0 : $result['maxPosition'];
	}
	
	
}