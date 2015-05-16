<?php
namespace application\protect\models;

class CommentModel extends \framework\core\Model
{
	public function tableName()
	{
		return 'comment';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	
	
}