<?php
namespace application\protect\models;

use framework;
use framework\App;

class UserModel extends \framework\core\Model
{
	const USER_STATE_BANNED = 0;//被禁止
	const USER_STATE_NORMAL = 1;//正常
	const USER_STATE_HANGUP = 2;//挂起

	public function tableName()
	{
		return 'user';	
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
	 * 添加用户
	 * Enter description here ...
	 * @param array $userInfo 用户的信息数组
	 */
	public function addUser(array $userInfo)
	{
		if(!empty($userInfo['password']) && strlen($userInfo['password']) < 60)
		{
			$userInfo['password'] = $this->hashPassword($userInfo['password']);
		}
		if(empty($userInfo['passkey']))
		{
			$userInfo['passkey'] = md5($userInfo['name'].time());
		}
		$user = new UserModel();//要写完整UserModel
//		var_dump($user);exit;
		foreach($userInfo as $field=>$value)
		{
			$user->$field = $value;
		}
		$user->add_time = time();
		$user->role_level = 1;
		$user->role_name = '幼儿园';
		$user->avatar_url = 'application/public/images/avatar.jpg';
		$result = $user->save();
		return $result;
		
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
		$doCache = FALSE;
		if (App::isComponentEnabled('Memcache'))
		{
			$userId = App::ins()->user->getId();
			$cacheKey = 'user_rules_'.$userId;
			$result = App::ins()->mem->get($cacheKey);
			if ($result !== FALSE)
			{
				return $result;
			}
			$doCache = TRUE;
		}
		
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
		if ($doCache)
		{
			App::ins()->mem->set($cacheKey, $merge);
		}
		return $merge;
	}
	
}