<?php
namespace application\protect\models;

class ForumsectionModel extends \framework\core\Model
{
	public function tableName()
	{
		return 'forum_section';
	}
	
	//子类获得模型对象的方法，通过调用父类的getModel()，传递子类的类名
	public static function model($className = __CLASS__)
	{
		return parent::getModel($className);
	}
	
	public function rules()
	{
		return array(
				array('name, master_name_list', 'required', '不能为空'),
				array('sort', 'number', '必须是非负整数'),
				array('sort', 'unique', '该排序已存在'),
				array('name', 'unique', '名称已存在'),
		);
	}
	
	
	
}