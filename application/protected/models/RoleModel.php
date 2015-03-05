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
				array('bonus_limit, downloaded_limit, uploaded_limit, register_time_limit', 'number', '必须是整数'),
				array('name', 'required', '不能为空'),
				array('ratio_limit', 'float', '必须是浮点数'),
		);
	}
	
	public function checkratio($ratio)
	{
		if (preg_match('/^(\s)/', $ratio))
		{
			return FALSE;//开始不能有空格
		}
		return is_numeric($ratio);//右边有空格is_numeric不能通过
	}
	
}