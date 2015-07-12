<?php
namespace application\protect\models;

//楼层模型

class CommenFloortModel extends \framework\core\Model
{
	public function tableName()
	{
		return 'comment_floor';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	
	
}