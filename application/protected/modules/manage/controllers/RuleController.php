<?php
class RuleController extends CommonController
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
	
	private function submit(Model $model, array $data, $action = 'insert')
	{
		if ($model->validate($data))
		{
			if ($data['parent_id'] > 0)
			{
				$sort = $data['sort'];
				unset($data['sort']);
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
			if ($result >= 0)
			{
				if (isset($sort) && $id)
				{
					$sql = "UPDATE rule SET sort=$sort WHERE path LIKE concat((SELECT path FROM rule WHERE id=$id),%)";
					$updateSort = $model->execute($sql);
					if ($updateSort === FALSE)
					{
						trigger_error('更新sort失败', E_USER_ERROR);exit;
					}
				}
				$this->redirect('manage/rule/list');
			}
		}
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
		$sql .= " ORDER BY sort ASC";		
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
				if ($id == $rule['id'])
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