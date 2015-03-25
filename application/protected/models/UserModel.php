<?php
class UserModel extends Model
{

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
		$result = $user->save();
		return $result;
		
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
			App::addRequirePath(LIB_PATH.'phpass-0.3'.DS);
			$hasher = new PasswordHash(8, false);
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
			App::addRequirePath(LIB_PATH.'phpass-0.3'.DS);
			$hasher = new PasswordHash(8, false);
			return $hasher->CheckPassword($inputPassword, $password);
			
		}
	}
	
	
}