<?php
namespace application\protect\models;


// use framework\App;


//权限   模型

class RuleModel extends \framework\core\Model
{
	
	private static $_ruleList = array();//权限列表
	
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
	
	public function initRule($ruleMvc)
	{
		if (!is_string($ruleMvc) || empty($ruleMvc))
		{
			return FALSE;
		}
		if (in_array($ruleMvc, self::$_ruleList))
		{
			return TRUE;
		}
		/*
		if (!App::ins()->user->isLogin())
		{
			//取普通用户组级别最低的角色，所以游客角色是不能删除的
			$roleGroup = RoleModel::ROLE_GROUP_NORMAR;
			$roleMin = $this->table('role')->where('role_group_id='.$roleGroup)->order('level ASC')->limit(1)->select();
			if (empty($roleMin))
			{
				return FALSE;
			}
			$roleId = $roleMin[0]['id'];
			
			$sql = "SELECT rule_key,rule_mvc FROM rule WHERE id IN (select rule_id FROM role_rule WHERE role_id=$roleId)";
		}
		else
		{
			//角色分组暂时不考虑
			
			$userId = App::ins()->user->getId();
			$sql = "SELECT rule_key,rule_mvc FROM rule WHERE id IN (select rule_id from role_rule where role_id = (select id FROM role WHERE level = (select role_level FROM user WHERE id=$userId)))";
		}
		$result = $this->findBySql($sql);
		if (empty($result))
		{
			return FALSE;
		}
		*/
		$ruleList = UserModel::model()->getRules();
		foreach ($ruleList as $rule)
		{
			self::$_ruleList[$rule['rule_key']] = $rule['rule_mvc'];
		}
// 		var_dump($ruleMvc);
// 		echo '<hr/>';
// 		var_dump(self::$_ruleList);exit;
		return in_array($ruleMvc, self::$_ruleList);
	}
	
	public function hasRule($ruleKey)
	{
		if (!is_string($ruleKey) || empty($ruleKey))
		{
			return FALSE;
		}
		return isset(self::$_ruleList[$ruleKey]);
	}
	
	/**
	 * 根据角色返回权限
	 * @param mixed $roldId 角色id，单个或者数组
	 * @return array 对应权限集合
	 */
	public function getRulesByRole($roleId)
	{
		if (!is_array($roleId))
		{
			$roleId = array($roleId);
		}
// 		var_dump($roleId);exit;
		$con = "id IN (SELECT distinct rule_id FROM role_rule WHERE role_id IN (".implode(',', $roleId)."))";
		$result = $this->where($con)->select();
		return $result;
	}
	
	
}