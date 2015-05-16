<?php
namespace application\protect\models;

//用户组模型
class RolegroupModel extends \framework\core\Model
{
	const ROLE_GROUP_NORMAR = 1;//普通用户级
	
	public function tableName()
	{
		return 'role_group';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	public function rules()
	{
		return array(
				array('name', 'required', '不能为空'),
				array('name', 'unique', '名称已经存在'),
		);
	}
	
	
}