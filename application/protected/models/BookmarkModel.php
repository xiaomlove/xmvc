<?php
class OptionModel extends Model
{
	
	public function tableName()
	{
		return 'bookmark';
	}
	
	//子类获得模型对象的方法，通过调用父类的getModel()，传递子类的类名
	public static function model($className = __CLASS__)
	{
		return parent::getModel($className);
	}
	
}