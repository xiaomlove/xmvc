<?php
namespace application\protect\models;

//角色   模型
class RoleModel extends \framework\core\Model
{
	const ROLE_GUEST_ID = 25;//两个特殊的角色ID，游客ID为25。数据库中有变需要更改之
	const ROLE_DEFAULT_ID = 22;//默认角色ID，幼儿园ID为22
	
	public function tableName()
	{
		return 'role';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	public function rules()
	{
		return array(
				array('bonus_limit, downloaded_limit, uploaded_limit, register_time_limit_value, level', 'number', '必须是非负整数'),
				array('name', 'required', '不能为空'),
				array('name', 'unique', '名称已经存在'),//只在新增场景中使用
				array('level', 'unique', '等级已经存在'),
				array('ratio_limit', 'float', '必须是浮点数'),
		);
	}
	
	public function getRoleGroupList()
	{
		$result = $this->table('role_group')->select();
		return $result;
	}
	
}