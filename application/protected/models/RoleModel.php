<?php
class RoleModel extends Model
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
				array('bonus_limit, downloaded_limit, uploaded_limit, register_time_limit, level', 'number', '必须是非负整数'),
				array('name', 'required', '不能为空'),
				array('name', 'unique', '名称已经存在'),
				array('level', 'unique', '等级已经存在'),
				array('ratio_limit', 'float', '必须是浮点数'),
		);
	}
	
	
}