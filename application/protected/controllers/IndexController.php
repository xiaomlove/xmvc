<?php
class IndexController extends CommonController
{
	public $layout = 'main';
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
		$testModel = TestModel::model();
		
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
		$userInfo = UserModel::model()->findByPk(App::ins()->user->getId());
		header("Access-Control-Allow-Origin: http://ip.taobao.com/service/");
		echo $this->render('home', array('userInfo'=>$userInfo));		
	}
	
	public function actionCheckRegister()
	{
		if(empty($_POST['username']) || empty($_POST['email']))
		{
			echo json_encode(array('code'=>0, 'msg'=>'没有用户名或者邮箱'));exit;
		}
		$userModel = UserModel::model();
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
		$register = $userModel->addUser($userInfo);
		if($register)
		{
			echo json_encode(array('code'=>1, 'msg'=>'注册成功，用户名是：'.$userInfo['name']));
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
		$userModel = UserModel::model();
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
	
	public function init()
	{
// 		echo '控制器初始化方法，实例化控制器时执行！<br/>';
// 		$this->setPageTitle('设置的标题');
// 		echo $this->pageTitle;
		
	}
}