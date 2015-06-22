<?php
namespace application\protect\modules\manage\controllers;

use application\protect\models\TorrentModel;
use application\protect\models\CategoryModel;
use framework\App;
// use application\protect\models as models;


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
			$field = trim($_POST['field']);
			$name = trim($_POST['name']);
			$_fields = TorrentModel::model()->_fields;//所有字段
			if (!in_array($field, $_fields))
			{
				echo json_encode(array('code' => -2, 'msg' => '没有该字段'));exit;
			}
			//检查名称是否已经存在
			$hadName = $model->where("name='$name' AND parent_id=0")->limit(1)->select();
			if (!empty($hadName))
			{
				echo json_encode(array('code' => -3, 'msg' => '该名称已存在'));exit;
			}
			//检查值是否已经存在
			$hadValue = $model->where("value='$field' AND parent_id=0")->limit(1)->select();
			if (!empty($hadValue))
			{
				echo json_encode(array('code' => -4, 'msg' => '该字段的分类项已存在'));exit;
			}
			
			$result = $model->addParent($name, $field);
			
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
			$field = trim($_POST['field']);
			$name = trim($_POST['name']);
			$id = $_GET['id'];
			$_fields = TorrentModel::model()->_fields;//所有字段
			if (!in_array($field, $_fields))
			{
				echo json_encode(array('code' => -2, 'msg' => '没有该字段'));exit;
			}
			
			$hadName = $model->where("name='$name' AND parent_id=0")->limit(1)->select();
			if (!empty($hadName) && $hadName[0]['id'] != $id)
			{
				echo json_encode(array('code' => -3, 'msg' => '该名称已存在'));exit;
			}
			
			$hadField = $model->where("value='$field' AND parent_id=0")->limit(1)->select();
			if (!empty($hadField) && $hadField[0]['id'] != $id)
			{
				echo json_encode(array('code' => -4, 'msg' => '该torrent字段已存在'));exit;
			}
			
			$result = $model->updateByPk($id, array('name' => strip_tags($name), 'value' => $field));
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
		$subList = $model->where("parent_id=".$parent['id'])->order('sn ASC,id ASC')->select();
		$html = $this->render('subcategorylist', array('parent' => $parent, 'subCategoryList' => $subList));
		echo $html;
	}
	
	public function actionAddSub()
	{
		if (App::ins()->request->isGet())
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
			$html = $this->render('subcategoryform', array('parent' => $parent, 'parentId' => $parent['id'] ,'model' => $model));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			if (empty($_POST['parent_id']) || !ctype_digit($_POST['parent_id']))
			{
				$this->goError();
			}
			$model = CategoryModel::model();
			$parent = $model->findByPk($_POST['parent_id']);
			if (empty($parent))
			{
				$this->goError();
			}
			if ($model->validate($_POST))
			{
				$valueCount = $model->where("parent_id={$_POST['parent_id']} AND value='{$_POST['value']}'")->count();
				if ($valueCount)
				{
					$model->setError('value', '该值已存在');
					goto A;
				}
				
				$nameCount = $model->where("parent_id={$_POST['parent_id']} AND name='{$_POST['name']}'")->count();
				if ($nameCount)
				{
					$model->setError('name', '该name已存在');
					goto A;
				}
				
				$category = new CategoryModel();
				$category->name = trim(strip_tags($_POST['name']));
				$category->sn = ($model->getMaxSn($_POST['parent_id'])) + 1;
				$category->value = intval($_POST['value']);
				$category->parent_id = intval($_POST['parent_id']);
				$result = $category->save();
				if ($result)
				{
					$this->redirect('manage/category/sublist', array('parent_id' => $_POST['parent_id']));
				}
				else
				{
					$model->setError('name', '未知错误');
				}
			}
			A:
			$model->setData($_POST);
			$html = $this->render('subcategoryform', array('parent' => $parent, 'parentId' => $parent['id'], 'model' => $model));
			echo $html;
		}
		
	}
	
	public function actionEditSub()
	{
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['id']) || !ctype_digit($_GET['id']))
			{
				$this->goError();
			}
			$model = CategoryModel::model();
			$category = $model->findByPk($_GET['id']);
			if (empty($category))
			{
				$this->goError();
			}
			$model->setData($category);
			$html = $this->render('subcategoryform', array('category' => $category, 'parentId' => $category['parent_id'] ,'model' => $model));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			if (empty($_POST['parent_id']) || !ctype_digit($_POST['parent_id']))
			{
				$this->goError();
			}
			$model = CategoryModel::model();
			$parent = $model->findByPk($_POST['parent_id']);
			$category = $model->findByPk($_POST['id']);
			if (empty($parent) || empty($category))
			{
				$this->goError();
			}
			if ($model->validate($_POST))
			{
				$hadValue = $model->where('parent_id='.$parent['id'].' AND value=\''.$_POST['value'].'\'')->limit(1)->select();
				if (!empty($hadValue) && $hadValue[0]['id'] != $_POST['id'])
				{
					$model->setError('value', '该值已存在！');
					goto A;
				}
				
				$hadName = $model->where('parent_id='.$parent['id'].' AND name=\''.$_POST['name'].'\'')->limit(1)->select();
				if (!empty($hadName) && $hadName[0]['id'] != $_POST['id'])
				{
					$model->setError('value', '该值已存在！');
					goto A;
				}
				
				$result = $model->updateByPk($_POST['id'], array(
						'name' => trim(strip_tags($_POST['name'])),
						'value' => intval($_POST['value']),
				));
				if ($result !== FALSE)
				{
					$this->redirect('manage/category/sublist', array('parent_id' => $_POST['parent_id']));
				}
				else 
				{
					$model->setError('name', '未知错误');
				}
			}
			A:
			$model->setData($_POST);
			$html = $this->render('subcategoryform', array('category' => $category, 'parentId' => $parent['id'], 'model' => $model));
			echo $html;
		}
	}
	
}