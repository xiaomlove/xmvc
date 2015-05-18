<?php
namespace application\protect\modules\manage\controllers;

use framework\App;
use application\protect\models\UserModel;
use application\protect\models\OptionModel;

class UserController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	public function actionUserlist()
	{
		$model = UserModel::model();
		$per = OptionModel::model()->get('manage_user_pagination');
		$per = $per ? $per : 10;
		$per = 2;//测试
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		$page = ctype_digit(strval($page)) ? $page : 1;
		$limit = ($page - 1)*$per.','.$per;
		$userList = $model->field('id, name, email, role_name, uploaded, downloaded, last_login_time')->order('add_time ASC')->limit($limit)->select();
// 		var_dump($userList);exit;
		$count = $model->count();
		$pagination = $this->getAjaxNavHtml($page, ceil($count/$per));
		if (isset($_GET['ajax']) && $_GET['ajax'] === 'true')
		{
			$html = $this->renderPartial('usertable', array('userList' => $userList, 'pagination' => $pagination));
			echo json_encode(array('code' => 1, 'msg' => '请求数据成功', 'data' => $html));
		}
		else
		{
			$html = $this->render('userlist', array('userList' => $userList, 'pagination' => $pagination));
			echo $html;
		}
		
		
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