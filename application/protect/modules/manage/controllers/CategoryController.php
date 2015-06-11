<?php
namespace application\protect\modules\manage\controllers;

use application\protect\models\CategoryModel;

use framework\App;
use application\protect\models as models;


class CategoryController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	public function actionList()
	{
		$model = CategoryModel::model();
		$categoryList = $model->order('sn ASC,id ASC')->select();
		$html = $this->render('categorylist', array('categoryList' => $categoryList));
		echo $html;
	}
	
	public function actionAddParent()
	{
		if (App::ins()->request->isPost())
		{
			if (empty($_POST['name']))
			{
				echo json_encode(array('code' => -1, 'msg' => '参数不全'));exit;
			}
			$modal = CategoryModel::model();
			$result = $modal->addParent($_POST['name']);
			
			if ($result)
			{
				echo json_encode(array('code' => 1, 'msg' => '添加成功', 'data' => $result));exit;
			}
			else
			{
				echo json_encode(array('code' => -1, 'msg' => '添加失败'));exit;
			}
		}
		else 
		{
			echo json_encode(array('code' => -1, 'msg' => '只能POST方式提交'));exit;
		}
		
	}
	
	public function actionEditParent()
	{
		if (App::ins()->request->isPost())
		{
			if (empty($_POST['name']) || empty($_GET['id']))
			{
				echo json_encode(array('code' => -1, 'msg' => '参数不全'));exit;
			}
			$modal = CategoryModel::model();
			$result = $modal->updateByPk($_GET['id'], array('name' => strip_tags($_POST['name'])));
// 			var_dump($result);			
			if ($result)
			{
				echo json_encode(array('code' => 1, 'msg' => '添加成功', 'data' => $result));exit;
			}
			else
			{
				echo json_encode(array('code' => 0, 'msg' => '没有变化'));exit;
			}
		}
		else
		{
			echo json_encode(array('code' => -1, 'msg' => '只能POST方式提交'));exit;
		}
	
	}
	
	public function actionExchangeSn()
	{
		if (App::ins()->request->isPost())
		{
			if (empty($_POST['id']) || empty($_POST['targetId']))
			{
				echo json_encode(array('code' => -1, 'msg' => '参数不全'));exit;
			}
			if (!ctype_digit($_POST['id']) || !ctype_digit($_POST['targetId']))
			{
				echo json_encode(array('code' => -1, 'msg' => '参数错误'));exit;
			}
			$model = CategoryModel::model();
			$result = $model->exchangeSn($_POST['id'], $_POST['targetId']);
			if ($result)
			{
				echo json_encode(array('code' => 1, 'msg' => '交换成功'));exit;
			}
			else 
			{
				echo json_encode(array('code' => -1, 'msg' => '交换失败'));exit;
			}
		}
		else
		{
			echo json_encode(array('code' => -1, 'msg' => '只能POST方式提交'));exit;
		}
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