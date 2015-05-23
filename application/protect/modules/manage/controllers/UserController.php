<?php
namespace application\protect\modules\manage\controllers;

use framework\App;
use application\protect\models as models;


class UserController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	public function actionUserlist()
	{
		$model = models\UserModel::model();
		$per = models\OptionModel::model()->get('manage_user_pagination');
		$per = $per ? $per : 10;
//		$per = 2;//测试
		$page = !empty($_GET['page']) ? $_GET['page'] : 1;
		$page = ctype_digit(strval($page)) ? $page : 1;
		$limit = ($page - 1)*$per.','.$per;
		$orderField = !empty($_GET['field']) ? $_GET['field'] : "id";
		$orderType = !empty($_GET['type']) ? $_GET['type'] : "ASC";
		if (!empty($_GET['keyword']))
		{
			$where = "name LIKE '%".$_GET['keyword']."%' OR email LIKE '%".$_GET['keyword']."%'";
		}
		else 
		{
			$where = "1";
		}
		$userList = $model->where($where)->field('id, name, email, role_name, uploaded, downloaded, this_login_time')->order("$orderField $orderType")->limit($limit)->select();
// 		var_dump($userList);exit;
		$count = $model->where($where)->count();
		$pagination = $this->getAjaxNavHtml($page, ceil($count/$per));
//		var_dump($pagination);exit;
		if (isset($_GET['ajax']) && $_GET['ajax'] === 'true')
		{
			$html = $this->renderPartial('usertable', array('userList' => $userList));
			echo json_encode(array('code' => 1, 'msg' => '请求数据成功', 'data' => array('tbody' => $html, 'pagination' => $pagination)));
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
	
	public function actionDetail()
	{
		if (empty($_GET['id']) || !ctype_digit($_GET['id']))
		{
			$this->goError();
		}
		$model = models\UserModel::model();
		$userInfo = $model->findByPk($_GET['id']);
		$roles = $model->getRoles($_GET['id']);
		$extraRules = $model->getExtraRules($_GET['id']);
		$html = $this->render('userdetail', array('userInfo' => $userInfo, 'roles' => $roles, 'extraRules' => $extraRules));
		echo $html;
	}
	
	public function actionGetUserUploadTorrents()
	{
		if (!empty($_GET['user_id']) && ctype_digit($_GET['user_id']))
		{
			$userId = $_GET['user_id'];
		}
		else
		{
			$userId = App::ins()->user->getId();
		}
		$per = models\OptionModel::model()->get('manage_user_detail_pagination');
		if (empty($per))
		{
			$per = 5;
		}
		$_GET['per'] = $per;
		$result = models\TorrentModel::model()->getList($_GET, "user_id=$userId");
//		var_dump($result);
		$pagination = $this->getAjaxNavHtml($result['page'], ceil($result['count']/$result['per']));
		$html = $this->renderPartial('useruploadtorrentslist', array('torrentList' => $result['data'], 'pagination' => $pagination));
// 		echo $html;exit;
		if (App::ins()->request->isAjax())
		{
			echo json_encode(array('code' => 1, 'msg' => '请求数据成功', 'data' => $html));
		}
	}
}