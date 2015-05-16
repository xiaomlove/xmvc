<?php
namespace application\protect\models;

//角色   模型
class RoleModel extends \framework\core\Model
{
	
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