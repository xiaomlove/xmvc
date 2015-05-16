<?php
namespace application\protect\modules\manage\controllers;

use framework\App as App;
use application\protect\models\UserModel;

class UserController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	public function actionUserlist()
	{
		$model = UserModel::model();
		$userList = $model->field('id, name, email, role_name, uploaded, downloaded, last_login_time')->order('add_time ASC')->select();
//		var_dump($userList);exit;
		$html = $this->render('userlist', array('userList' => $userList));
		echo $html;
	}
	
	public function actionUseradd()
	{
		if (App::ins()->request->isGet())
		{
			$html = $this->render('userform');
			echo $html;
		}
		elseif(App::ins()->request->isPost())
		{
			echo '提交添加section';
		}
	}
}