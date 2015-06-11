<?php
namespace application\protect\models;

use framework\App;

class CategoryModel extends \framework\core\Model
{

	public function tableName()
	{
		return 'category';	
	}
	
	public static function model()
	{
		return parent::getModel(__CLASS__);
	}
	/**
	 * 通过用户名查找用户，可返回部分字段，默认返回id,name,password
	 * @param unknown $name
	 * @param mixed $fields
	 * @return array
	 */
	public function findByName($name, $fields = 'id, name, password')
	{
		if(empty($name) || !is_string($name))
		{
			return NULL;
		}
		if(!empty($fields) && is_array($fields))
		{
			$fields = implode(',', $fields);
		}
		$result = $this->field($fields)->where('name = :name', array(':name'=>$name))->limit(1)->select();
		return empty($result) ? NULL : $result[0];
	}
	
	/**
	 * 通过邮箱查找用户，可返回部分字段，默认返回id,name,password
	 * @param unknown $name
	 * @param mixed $fields
	 * @return array
	 */
	public function findByEmail($email, $fields = 'id, name, password')
	{
		if(empty($email) || !is_string($email))
		{
			return NULL;
		}
		if(!empty($fields) && is_array($fields))
		{
			$fields = implode(',', $fields);
		}
		$result = $this->field($fields)->where('email = :email', array(':email'=>$email))->limit(1)->select();
		return empty($result) ? NULL : $result[0];
	}
	/**
	 * 登陆操作，如果记住登陆信息，默认30天
	 * Enter description here ...
	 */
	public function login($id, $name, $password, $remember = TRUE, $expire = 108000)
	{
		$sql = "UPDATE user SET last_login_time=this_login_time,this_login_time=".$_SERVER['REQUEST_TIME']." WHERE id=$id";
		$this->execute($sql);
		$result = App::ins()->user->setLogin($id, $name, $password, $remember, $expire);
		return $result;
	}
	/**
	 * 添加一级分类
	 * Enter description here ...
	 * @param array $name 一级分类名称
	 */
	public function addParent($name)
	{
		$maxSn = self::getMaxSn() + 1;
		$name = strip_tags($name);
		$sql = "INSERT INTO ".self::tableName()." (name,sn) VALUES ('$name','$maxSn')";
		return $this->execute($sql);
	}
	/**
	 * 获得当前最大的排序序号，首条记录默认99
	 * Enter description here ...
	 */
	public function getMaxSn()
	{
		$sql = 'SELECT max(sn) as maxSn FROM '.self::tableName();
		$result = $this->findBySql($sql);
		return empty($result[0]['maxSn']) ? 99 : $result[0]['maxSn'];
	}
	/**
	 * 为用户添加角色，如果不指定角色id，取普通用户组的默认角色
	 * Enter description here ...
	 * @param unknown_type $userId
	 * @param unknown_type $roleId
	 */
	public function addUserRole($userId, $roleId = '')
	{
		if (empty($roleId))
		{
			$roleId = RoleModel::ROLE_DEFAULT_ID;
		}
		$sql = "SELECT * FROM role WHERE id=$roleId";
		$roleInfo = $this->findBySql($sql);
		if (empty($roleInfo))
		{
			trigger_error('默认角色不存在', E_USER_ERROR);
			return FALSE;
		}
		$roleInfo = $roleInfo[0];
//		var_dump($roleInfo);exit;
		$delSql = "DELETE FROM user_role WHERE user_id=$userId AND role_group_id=".$roleInfo['role_group_id'];
		$del = $this->execute($delSql);
		$insertSql = "INSERT INTO user_role (user_id, role_id, role_group_id) VALUES ($userId, $roleId, {$roleInfo['role_group_id']})";
		return $this->execute($insertSql);
	}
	
	public function hashPassword($password)
	{
		if(empty($password) || !is_string($password))
		{
			return FALSE;
		}
		if(function_exists('password_hash'))
		{
			return password_hash($password, PASSWORD_DEFAULT);
		}
		else
		{
			//App::addRequirePath(LIB_PATH.'phpass-0.3'.DS);
			$hasher = new \framework\lib\phpass\PasswordHash(8, false);
			$hashPassword = $hasher->HashPassword($password);
			return $hashPassword;
		}
		
	}
	
	public function checkPassword($inputPassword, $password)
	{
		if(function_exists('password_verify'))
		{
			return password_verify($inputPassword, $password);
		}
		else
		{
			$hasher = new \framework\lib\phpass\PasswordHash(8, false);
			return $hasher->CheckPassword($inputPassword, $password);
			
		}
	}
	
	/**
	 * 获得用户拥有的角色
	 * @param return array 二维数组，每个角色为一个元素
	 */
	public function getRoles($userId = '')
	{
		$isLogin = App::ins()->user->isLogin();
		if ($isLogin)
		{
			if (empty($userId))
			{
				$userId = App::ins()->user->getId();
			}
			$sql = "SELECT a.*,b.name as role_group_name FROM role a LEFT JOIN role_group b 
					ON a.role_group_id=b.id WHERE a.id IN 
					(SELECT role_id FROM user_role WHERE user_id=$userId)";
			return $this->findBySql($sql);
		}
		else
		{
			$result = $this->table('role')->where('role_group_id='.RolegroupModel::ROLE_GROUP_NORMAR)->order('level ASC')->limit(1)->select();
			return empty($result) ? NULL : $result;
		}
		
	}
	/**
	 * 获得额外的权限，除角色外的权限
	 * Enter description here ...
	 * @param unknown_type $userId
	 * @param return array
	 */
	public function getExtraRules($userId = '')
	{
		$isLogin = App::ins()->user->isLogin();
		if ($isLogin)
		{
			if (empty($userId))
			{
				$userId = App::ins()->user->getId();
			}
			$sql = "SELECT * FROM rule WHERE id IN (SELECT rule_id FROM user_rule WHERE user_id=$userId)";
			$result = $this->findBySql($sql);
			return $result;
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * 获得当前用户的所有权限，角色上的+额外的。不登陆为游客的
	 */
	public function getRules()
	{
		$roles = self::getRoles();//角色
 	
		if (empty($roles))
		{
			return NULL;
		}

		$roleId = array();
		foreach ($roles as $role)
		{
			$roleId[] = $role['id'];
		}
		$rules = RuleModel::model()->getRulesByRole($roleId);
//		var_dump($rules);
//		echo '<hr/>';
		$extraRules = self::getExtraRules();
//		echo '<hr/>';
//		var_dump($extraRules);
		$merge = array_merge($rules, $extraRules);
//		echo '<hr/>';
//		var_dump($merge);
//		exit;
		return $merge;
	}
	
}