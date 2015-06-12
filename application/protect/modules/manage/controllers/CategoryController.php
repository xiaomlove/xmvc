<?php
namespace application\protect\modules\manage\controllers;

use application\protect\models\TorrentModel;

use application\protect\models\CategoryModel;

use framework\App;
use application\protect\models as models;


class CategoryController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	public function actionParentList()
	{
		$model = CategoryModel::model();
		$categoryList = $model->where('parent_id=0')->order('sn ASC,id ASC')->select();
		$html = $this->render('categorylist', array('categoryList' => $categoryList));
		echo $html;
	}
	
	public function actionAddParent()
	{
		if (App::ins()->request->isPost())
		{
			if (empty($_POST['name']) || empty($_POST['field']))
			{
				echo json_encode(array('code' => -1, 'msg' => '参数不全'));exit;
			}
			$model = CategoryModel::model();
			$field = $_POST['field'];
			$_fields = TorrentModel::model()->_fields;//所有字段
			if (!in_array($field, $_fields))
			{
				echo json_encode(array('code' => -2, 'msg' => '没有该字段'));exit;
			}
			$isHaved = $model->where("value='$field'")->limit(1)->select();
			if (!empty($isHaved))
			{
				echo json_encode(array('code' => -2, 'msg' => '该字段的分类项已存在'));exit;
			}
			$result = $model->addParent($_POST['name'], trim($field));
			
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
			if (empty($_POST['name']) || empty($_GET['id']) || empty($_POST['field']))
			{
				echo json_encode(array('code' => -1, 'msg' => '参数不全'));exit;
			}
			$model = CategoryModel::model();
			$field = $_POST['field'];
			$id = $_GET['id'];
			$_fields = TorrentModel::model()->_fields;//所有字段
			if (!in_array($field, $_fields))
			{
				echo json_encode(array('code' => -2, 'msg' => '没有该字段'));exit;
			}
			$haved = $model->where("value='$field'")->limit(1)->select();
			if (!empty($haved) && $haved[0]['id'] != $id)
			{
				echo json_encode(array('code' => -2, 'msg' => '该字段的分类项已存在'));exit;
			}
			$result = $model->updateByPk($id, array('name' => strip_tags($_POST['name']), 'value' => trim($field)));
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
	
	public function actionSubList()
	{
		if (empty($_GET['parent_id']) || !ctype_digit($_GET['parent_id']))
		{
			$this->goError();
		}
		$model = CategoryModel::model();
		$parent = $model->findByPk($_GET['parent_id']);
		if (empty($parent))
		{
			$this->goError();
		}
		$subList = $model->where("parent_id=".$parent['id'])->select();
		$html = $this->render('subcategorylist', array('parent' => $parent, 'subCategoryList' => $subList));
		echo $html;
	}
}