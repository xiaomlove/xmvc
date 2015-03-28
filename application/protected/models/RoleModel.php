<?php
class RoleModel extends Model
{
	const ROLE_GROUP_NORMAR = 1;//普通用户级
	const ROLE_GROUP_MANAGE = 2;//管理组
	const ROLE_GROUP_TECHNOLOGY = 3;//技术组
	const ROLE_GROUP_DEVELOP = 4;//开发组
	const ROLE_GROUP_VIP = 5;//VIP组
	
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
	
	
}