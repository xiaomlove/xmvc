<?php
namespace application\protect\modules\manage\controllers;

use framework\App as App;
use application\protect\models\RuleModel;

class RuleController extends \application\protect\controllers\CommonController
{
	public $layout = 'manage';
	
	public function actionList()
	{
		$model = RuleModel::model();
		$ruleList = $model->order('sort ASC,path ASC')->select();
		$html = $this->render('rulelist', array('ruleList' => $ruleList));
		echo $html;
	}
	
	public function actionEdit()
	{
		$model = RuleModel::model();
		if (App::ins()->request->isGet())
		{
			if (empty($_GET['id']) || !ctype_digit($_GET['id']))
			{
				$this->goError();
			}
			$rule = $model->findByPk($_GET['id']);
			$model->setData($rule);
			$html = $this->render('ruleform', array('model' => $model));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			if (empty($_POST['id']) || !ctype_digit($_POST['id']))
			{
				$this->goError();
			}
			$this->submit($model, $_POST, 'update');
		}
	}
	
	public function actionAdd()
	{
		$model = RuleModel::model();
		if (App::ins()->request->isGet())
		{
			$html = $this->render('ruleform', array('model' => $model));
			echo $html;
		}
		elseif (App::ins()->request->isPost())
		{
			$this->submit($model, $_POST, 'insert');
		}
		
	}
	
	private function submit(\framework\core\Model $model, array $data, $action = 'insert')
	{
		if ($model->validate($data))
		{
			if ($data['parent_id'] > 0)//非一级权限，sort要跟其父权限的sort一致
			{
				$parent = $model->findByPk($data['parent_id']);
				if (empty($parent))
				{
					$model->setError('parent_id', '父权限不存在');
					goto A;
				}
				$data['sort'] = $parent['sort'];
			}
			else 
			{
				$isParent = TRUE;
			}
			
			if ($action === 'insert')
			{
				$result = $model->insert($data);
				$id = $result;
			}
			elseif ($action === 'update')
			{
				$result = $model->updateByPk($data['id'], $data);
				$id = $data['id'];
			}
			
			if ($result !== FALSE)
			{
				
				if (isset($isParent) && $isParent)
				{
					//更新path及level，一级权限path为其自身id
					$updatePathLevel = $model->updateByPk($id, array('path' => $id, 'level' => 1));
					if ($updatePathLevel === FALSE)
					{
						$model->setError('parent_id', '更新一级权限path&level失败');
						goto A;
					}
					//当它为一级权限，改变了其sort其后代也跟着变
					$sql = "UPDATE rule SET sort={$data['sort']} WHERE path LIKE '{$id},%'";
					//$sql = "UPDATE rule SET sort={$data['sort']} WHERE path LIKE concat((SELECT path FROM rule WHERE id=$id),%)";
					$updateSort = $model->execute($sql);
					if ($updateSort === FALSE)
					{
						trigger_error('更新sort失败', E_USER_ERROR);exit;
					}
				}
				else
				{
					//非一级权限，sort不会被改变，只需要更新path及level
					$updatePathLevel = $model->updateByPk($id, array('path' => $parent['path'].','.$id, 'level' => $parent['level']+1));
					
					if ($updatePathLevel === FALSE)
					{
						$model->setError('parent_id', '更新非一级权限path&level失败');
						goto A;
					}
				}
				
				$this->redirect('manage/rule/list');
			}
			else 
			{
				$model->setError('name', 'submit执行失败');
			}
		}
		A:
		$model->setData($data);
		$html = $this->render('ruleform', array('model' => $model));
		echo $html;
	}
	
	protected function getParentSelect($name, $id = 0, $parentId = 0)
	{
		$model = RuleModel::model();
		if ($id > 0)
		{
// 			if ($parentId > 0)
// 			{
				$sql = "SELECT * FROM rule WHERE path NOT LIKE concat((SELECT path FROM rule WHERE id=$id),'%')";
// 			}
// 			else
// 			{
// 				$sql = "SELECT * FROM rule WHERE id !=$id AND path NOT LIKE '0,$id%'";
// 			}
		}
		else
		{
			$sql = "SELECT * FROM rule";
		}
		$sql .= " ORDER BY sort ASC,path ASC";		
		$parentList = $model->findBySql($sql);
// 		echo '<pre/>';
// 		var_dump($parentList);exit();
		$selectHtml = "<select class=\"form-control\" id=\"$name\" name=\"$name\">";
		$selectHtml .= "<option value=\"0\">无(一级权限)</option>";
		if (!empty($parentList))
		{
			foreach ($parentList as $rule)
			{
				$selected = "";
				if ($parentId == $rule['id'])
				{
					$selected = " selected";
				}
				$selectHtml .= "<option value=\"{$rule['id']}\"{$selected}>".str_repeat("----", $rule['level']-1).$rule['name']."</option>";
			}
			$selectHtml .= "</select>";
		}
		return $selectHtml;
	}
}