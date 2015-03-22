<?php
class RuleModel extends Model
{
	public function tableName()
	{
		return 'rule';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	
	public function rules()
	{
		return array(
				array('name, rule_key, rule_mvc', 'required', '不能为空'),
				array('sort', 'number', '请输入正整数'),
				array('rule_key, rule_mvc', 'unique', '已经存在'),
		);
	}
	
	
}