<?php
namespace application\protect\controllers;

use framework\component\Db as Db;
use framework\App as App;
use application\protect\models as models;


class IndexController extends CommonController
{
	public $layout = 'main';
	public $admitActions = array('CheckRegister', 'CheckLogin', 'Logout', 'GetIpInfo', 'Ueditor', 'Error', 'About');
	
	public function actionIndex()//action不接收参数运行，参数通过$_GET等获得
	{	
		echo 'this is IndexController, testAction<br/>';
// 		var_dump($this);
// 		$this->redirect('login');
		$db = Db::getInstance();
		echo 'serverVersion:'.$db->serverVersion.'<br/>';
		echo 'clientVersion:'.$db->clientVersion.'<br/>';
		echo 'serverInfo:'.$db->serverInfo.'<br/><br/><br/>';
// 		echo '<pre>';
		$testModel = models\TestModel::model();
		
		$info = $testModel->cache()->active()->findByPk(12);
		var_dump($info);
		$info1 = $testModel->cache()->findByPk(18);
		var_dump($info1);
		$data = array('a' => '这是a', 'b'=>'这是b');
		$this->layout = 'main';
		$html = $this->render('index', array('data'=>$data));
		echo $html;
		$isAjax = App::ins()->request->isAjax();
		var_dump($isAjax);	
	}
	
	public function actionRegister()
	{
		$this->setPageTitle('注册');
		echo $this->render('register');
	}
	
	public function actionLogin()
	{
		$this->setPageTitle('登陆');
		echo $this->render('login');
	}
	
	public function actionHome()
	{
		$this->setPageTitle('首页');
		$this->layout = 'tinypt';
		$userInfo = models\UserModel::model()->findByPk(App::ins()->user->getId());
		header("Access-Control-Allow-Origin: http://ip.taobao.com/service/");
		echo $this->render('home', array('userInfo'=>$userInfo));		
	}
	
	public function actionCheckRegister()
	{
		if(empty($_POST['username']) || empty($_POST['email']))
		{
			echo json_encode(array('code'=>0, 'msg'=>'没有用户名或者邮箱'));exit;
		}
		$userModel = models\UserModel::model();
		$userInfo = $userModel->findByName($_POST['username']);
		if(!empty($userInfo))
		{
			echo json_encode(array('code'=>-1, 'msg'=>'用户名已存在'));exit;
		}
		$userInfo = $userModel->findByEmail($_POST['email']);
		if(!empty($userInfo))
		{
			echo json_encode(array('code'=>-2, 'msg'=>'邮箱已存在'));exit;
		}
		$userInfo = array(
			'name'=>$_POST['username'], 
			'password'=>$_POST['password'], 
			'email'=>$_POST['email'],
		);
		$userId = $userModel->addUser($userInfo);
		if($userId)
		{
			$addRole = $userModel->addUserRole($userId);
			if (addRole)
			{
				echo json_encode(array('code'=>1, 'msg'=>'注册成功，用户名是：'.$userInfo['name']));
			}
			else 
			{
				echo json_encode(array('code'=>-1, 'msg'=>'注册成功，用户名是：'.$userInfo['name'].'，但添加默认角色失败'));
			}
		}
		else
		{
			echo json_encode(array('code'=>-3, 'msg'=>'注册失败，提交的数据是：'.json_encode($_POST)));
		}
	}
	
	public function actionCheckLogin()
	{
		if(empty($_POST['name']) || empty($_POST['password']))
		{
			echo json_encode(array('code'=>0, 'msg'=>'没有用户名或者密码'));exit;
		}
		$userModel = models\UserModel::model();
		$userInfo = $userModel->findByName($_POST['name']);
		if(empty($userInfo))
		{
			echo json_encode(array('code'=>-1, 'msg'=>'用户不存在'));exit;
		}
		if(!$userModel->checkPassword($_POST['password'], $userInfo['password']))
		{
			echo json_encode(array('code'=>-2, 'msg'=>'密码不正确'));exit;
		}
		$login = $userModel->login($userInfo['id'], $_POST['name'], $userInfo['password']);
		
		if($login)
		{
			echo json_encode(array('code'=>1, 'msg'=>'登陆成功'));
		}
		else
		{
			echo json_encode(array('code'=>-3, 'msg'=>'登陆失败，提交的数据是：'.json_encode($_POST)));
		}
	}
	
	public function actionLogout()
	{
		$logout = App::ins()->user->setLogout();
	
		if($logout)
		{
			echo json_encode(array('code'=>1, 'msg'=>'退出成功'));
		}
		else
		{
			echo json_encode(array('code'=>-1, 'msg'=>'退出失败'));
		}
	}
	
	public function actionGetIpInfo()
	{
		$ip = $_GET['ip'];
		$result = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.trim($ip));
		if($result !== FALSE)
		{
			echo $result;
		}
		else 
		{
			echo json_encode(array('code'=>-1));
		}
	}
	
	public function actionUeditor()
	{
		App::setConfig('noLog', TRUE);
        $path = APP_PATH.'assets/lib/ueditor/php/';
		require $path.'controller2.php';
	}
	
	public function actionError()
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_TIMEOUT, 3);
		curl_setopt($curl, CURLOPT_URL, 'http://cn.bing.com/HPImageArchive.aspx?idx=0&n=1');
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$str = curl_exec($curl);
		$imgurl = '';
		if(preg_match("/<url>(.+?)<\/url>/ies", $str, $matches)){
			$imgurl = 'http://cn.bing.com'.$matches[1];
		}
		echo $this->renderPartial('error', array('bgImg' => $imgurl));
	}
	
	/**
	 * websocket聊天返回
	 */
	public function actionTalk()
	{
		
	}
	
	public function actionAbout()
	{
		$this->layout = 'tinypt';
		echo $this->render('about');
	}
	
	
}