<?php
class RuleModel extends Model
{
	private $_ruleList = array();
	
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
				array('sort', 'number', '请输入非负正整数'),
				array('rule_key, rule_mvc', 'unique', '已经存在'),
		);
	}
	
	public function hasRule($ruleKey)
	{
		if (!is_string($ruleKey) || empty($ruleKey))
		{
			return FALSE;
		}
		if (!App::ins()->user->isLogin())
		{
			$ruleList = $this->where('');
		}
		
	}
	
	
}