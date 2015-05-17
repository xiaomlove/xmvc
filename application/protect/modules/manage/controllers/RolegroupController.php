<?php
namespace application\protect\modules\manage\controllers;

use framework\App;
use application\protect\models\RolegroupModel;


//用户组  控制器

class RolegroupController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	public function actionList()
	{
		$model = RolegroupModel::model();
		$roleGroupList = $model->select();
// 		var_dump($roleGroupList);exit;
		$html = $this->render('rolegrouplist', array('roleGroupList' => $roleGroupList));
		echo $html;
	}
	
	public function actionEdit()
	{
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['id']) || !ctype_digit($_GET['id']))
			{
				$this->goError();
			}
			$model = RolegroupModel::model();
			$roleGroupInfo = $model->findByPk($_GET['id']);
			if (empty($roleGroupInfo))
			{
				$this->goError();
			}
			$model->setData($roleGroupInfo);
			$html = $this->render('rolegroupform', array('model' => $model));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			if (empty($_POST['id']) || !ctype_digit($_POST['id']))
			{
				$this->goError();
			}
			$model = RolegroupModel::model();
			$roleGroupInfo = $model->findByPk($_POST['id']);
			if (empty($roleGroupInfo))
			{
				$this->goError();
			}
			if ($model->validate($_POST))
			{
				$result = $model->updateByPk($_POST['id'], array('name' => $_POST['name']));
				if ($result !== FALSE)
				{
					$this->redirect('manage/rolegroup/list');
				}
				$model->setError('id', '未知原因，更新失败');
			}
			$model->setData($_POST);
			$html = $this->render('rolegroupform', array('model' => $model));
			echo $html;
		}
		
	}
	
	public function actionAdd()
	{
		$model = RolegroupModel::model();
		if (App::ins()->request->isGet())
		{
			$html = $this->render('rolegroupform', array('model' => $model));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			if ($model->validate($_POST))
			{
				$roleGroup = new RolegroupModel();
				$roleGroup->name = $_POST['name'];
				$result = $roleGroup->save();
				if ($result)
				{
					$this->redirect('manage/rolegroup/list');
				}
				$model->setError('name', '未知原因，保存失败');
			}
			$model->setData($_POST);
			$html = $this->render('rolegroupform', array('model' => $model));
			echo $html;
		}
	}
	
	private function _submit($model, $data)
	{
		if ($model->validate($data))
		{
			$roleGroup = new RolegroupModel();
			$roleGroup->name = $data['name'];
			
		}
		$model->setData($data);
		$html = $this->render('rolegroupform', array('model' => $model));
		echo $html;
	}
	
}